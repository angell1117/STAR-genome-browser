#include <iostream>
#include <string>
#include <unistd.h>
#include <getopt.h>
#include <dirent.h>
#include <math.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <sys/file.h>
#include <sys/time.h>
#include <boost/regex.hpp>
#include <fcntl.h>

#include "utils.h"
#include "FileUtils.h"
#include "ConfUtils.h"
#include "NGSDatabase.h"
#include "db_cxx.h"

using namespace std;
using namespace boost;

int compare_int(Db *dbp, const Dbt *a, const Dbt *b)
{
    return memcmp(a->get_data(),b->get_data(),10);
} 
int compare(Db *dbp, const Dbt *a, const Dbt *b)
{
    int* aa = (int*)a->get_data();
    int* bb = (int*)b->get_data();
    if(*aa > *bb) return 1;
    else if(*aa == *bb) return 0;
    else return -1;
} 
void errcall(const DbEnv *dbenv, const char *errpfx, const char *msg)
{
    //cout << "message: " << (errpfx ? errpfx : "") << ": " << msg << endl;
}
NGSDatabase::NGSDatabase()
{
    delimiter = '\t';
    CACHE_SIZE = 50*16*1048576;
    MAX_GENOME_SIZE = 250000000;
}
NGSDatabase::NGSDatabase(int csize)
{
    delimiter = '\t';
    CACHE_SIZE = csize*1048576;
    MAX_GENOME_SIZE = 250000000;
}
NGSDatabase::~NGSDatabase()
{
}
void NGSDatabase::print_error(int ret)
{
    if(ret != 0)
    {
        printf("ERROR: %s\n",db_strerror(ret));
        exit(-1);
    }
}
string NGSDatabase:: TrimDir(char* dir)
{
    string basedir = Trim(dir);
    int len = basedir.length();
    if(len < 1) return basedir;
    if(basedir.at(len-1) != '/') basedir += '/';
    return basedir;
}
void NGSDatabase::GetMeta(char*line,_meta& meta)
{
    Trim(line);
    MakeLower(line);

    char* p = strstr(line,"fixedstep");
    if(p){
        int pos = p - line;
        *(line+pos+6) = 'a';
        meta.steptype = 1;
    }
    p = strstr(line,"variablestep");
    if(p){
        int pos = p - line;
        *(line+pos+9) = 'a';
        meta.steptype = 0;    
    }

    char value[128];
    ConfUtils conf;
    int ret;

    ret = conf.GetPara(line,"chrom",value);
    if(ret) strcpy(meta.chrom,value);
    ret = conf.GetPara(line,"start",value);
    if(ret) meta.start = atoi(value);
    ret = conf.GetPara(line,"step",value);
    if(ret) meta.step = atoi(value);
    ret = conf.GetPara(line,"span",value);
    if(ret) meta.span = atoi(value);
    if(meta.span <= 1) meta.span = meta.step;
}
void NGSDatabase::List(char* db_dir) 
{
    struct stat stat;

    string basedir = TrimDir(db_dir);

    DIR * dp;
    struct dirent *filename;

    dp = opendir(basedir.c_str());
    if(!dp)
    {
        fprintf(stderr,"open directory error\n");
        return;
    }
    
    int count = 0;
    string tables[20480];
    while((filename=readdir(dp))!=NULL)
    {
        string fname = basedir + filename->d_name;
        if(lstat(fname.c_str(), &stat) < 0)
        {
            perror("lstat error");
            break;
        }
        if(!S_ISDIR(stat.st_mode)) continue;
     
        tables[count] = filename->d_name;
        count++;
    }
    closedir(dp);

    for(int i = 0;i < count; i++)
    printf("%-10s\n", tables[i].c_str());
    printf("total %d tables!\n", count);
}
int NGSDatabase::Count(string db_name)
{
    Dbt key, data;
    Dbc *p_cur;
    char ekey[10];

    Db* dbp = new Db(0,0);
    u_int32_t flags = DB_RDONLY;
    dbp->set_cachesize(0, CACHE_SIZE, 0);

    int ret = dbp->open(NULL, db_name.c_str(), NULL, DB_BTREE, flags,0);
    print_error(ret);

    sprintf(ekey,"%010d",0);
    memset(&key, 0, sizeof(key));
    key.set_data(ekey);
    key.set_size(11);
    key.set_ulen(11);
    key.set_flags(DB_DBT_USERMEM);

    dbp->cursor(NULL, &p_cur, 0);

    int count = 0;
    for(ret = p_cur->get(&key, &data, DB_SET_RANGE);ret == 0;
        ret = p_cur->get(&key, &data, DB_NEXT))
     {
        count++;
    }
    
    if(!p_cur) p_cur->close();
    dbp->close(0);
    return count;
}
int NGSDatabase::GetCount(string db_name,char* db_dir) 
{
    string basedir = TrimDir(db_dir);
    basedir += db_name + "/";

    int count=0;
    for(int i = 0; i < MAX_CHR; i++)
    {
        char str[4];
        sprintf(str,"%02d",i);
        string _dbname = basedir+db_name+".db"+str;
        int ret = Count(_dbname);
        printf("%s, total %d records!\n",_dbname.c_str(),ret);
        count += ret;
    }
    printf("Total %d records!\n",count);
    return count;
}
void NGSDatabase::DropTable(string db_name,char* db_dir) 
{
    string basedir = TrimDir(db_dir);
    basedir += db_name + "/";

    for(int i = 0; i<MAX_CHR; i++)
    {
        char str[6][64];
        sprintf(str[0], "%02d", i);
        sprintf(str[1], "%02d-10", i);
        sprintf(str[2], "%02d-100", i);
        sprintf(str[3], "%02d-1000", i);
        sprintf(str[4], "%02d-10000", i);
        sprintf(str[5], "%02d-100000", i);
        for(int j = 0; j < 6; j++)
        {
            string _dbname = basedir + db_name + ".db" + str[j];
            int ret = remove(_dbname.c_str());
            if(ret == 0) printf("Successfully remove %s\n",_dbname.c_str());
            else printf("Failed to remove:%s\n",_dbname.c_str());
        }
    }
}

void NGSDatabase::DropDB(char* db_dir) 
{
    struct stat stat;

    string basedir = TrimDir(db_dir);

    DIR * dp;
    struct dirent *filename;

    dp = opendir(basedir.c_str());
    if(!dp)
    {
        fprintf(stderr,"open directory error\n");
        return;
    }
    
    int count = 0;
    string last;
    while((filename=readdir(dp))!=NULL)
    {
        string fname = basedir + filename->d_name;
        string dbname = filename->d_name;
        if(lstat(fname.c_str(), &stat) < 0)
        {
            perror("lstat error");
            break;
        }
        if(!S_ISDIR(stat.st_mode) || dbname == "." || dbname == "..") continue;
        count++;
        DropTable(dbname, db_dir);
    }
    closedir(dp);
    printf("Total %d tracks were processed!\n",count);
}

void NGSDatabase::Deposit(string fname,string db_name, string format, const char* db_dir) 
{
    char line[1024];
    Dbt key, data;
    u_int32_t flags;
    int ret;
    int selfDefine = 0;

    _db_record db_record;
    _db_record old_record;

    Db* dbp[MAX_CHR];

    regex expression("(-?[0-9]+):(-?[0-9]+):(-?[0-9]+):(-?[0-9]+):(-?[0-9]+):(-?[0-9]+)");
    string in = format;
    if(in == "bowtie") in  = "3:4:-1:2:-1:5";
    if(in == "bowtiesam") in  = "3:6:7:8:-1:11";
    if(in == "sam") in  = "3:4:-1:-1:-1:10";

    cmatch what;
    int idx[6];
    for(int i = 0;i < 6; i++) idx[i] = -1;
    if(regex_match(in.c_str(), what, expression))
    {
        selfDefine = 1;
        for(unsigned int j=1;j<what.size();j++) 
        {
            idx[j-1] = atoi(what[j].str().c_str());
        }
    }
    if(!selfDefine && in != "eland" && in != "elandmulti" && in != "elandexport" && 
        in != "gff" && in != "mapview" && in != "wig" && in != "bed") 
    return;

    string basedir = db_dir;
    basedir += db_name + "/";

    ret = mkdir(basedir.c_str(), 0777);
    cout<<"mkdir return code "<<ret<<endl;

    for(int i = 0; i < MAX_CHR; i++)
    {
        dbp[i] = new Db(0,0);
        dbp[i]->set_flags(DB_DUP);
        dbp[i]->set_bt_compare(compare_int);
        dbp[i]->set_errcall(errcall);
        dbp[i]->set_cachesize(0, CACHE_SIZE, 0);

        flags = DB_CREATE;
        
        char str[4];
        sprintf(str,"%02d",i);
        string _dbname = basedir+"__db."+db_name+".db"+str;
        remove(_dbname.c_str());
        string dbname = basedir+db_name+".db"+str;
        remove(dbname.c_str());
        ret = dbp[i]->open(NULL, dbname.c_str(), NULL, DB_BTREE, flags,0);
        print_error(ret);
    }

    FileUtils file;
    ret = file.OpenFileForRead(fname);
    if(!ret)
    {
        cout<<"can't open file "<<fname.c_str()<<" for read!"<<endl;
        exit(-1);
    }

    ret = file.AllocBuffer();
    if(!ret)
    {
        cout<<"can't allocate memory!"<<endl;
        exit(-1);
    }
    ret = file.FirstRead();
    if(!ret) 
    {
        file.Release();
        cout<<"Read file failed!"<<endl;
        exit(-1);
    }

    cout<<"Begin to process:"<<endl;

    int k = 0;
    int total = 0;
    string fields[20];
    char s_value[128];
    
    memset(&old_record, 0, sizeof(_db_record));
    double sum_val = 0;
    int old_index = -1;
    int tag_len = 0;

    //WIG format
    _meta meta;
    strcpy(meta.chrom,"");
    meta.start = meta.step = meta.span = meta.steptype = 1;
    int section_count = 0;

    while(1)
    {
        int not_end = file.GetLine(line,1024);
        if(!not_end)
        {
           Trim(line);
           if(line[0] == '\0') break;
        }

        int num = 20;
        int index = -1;
        if(in == "wig")
        {
            if(strstr(line,"="))
            {
                GetMeta(line,meta);
                section_count = 0;
                continue;
            }

            index = chr2id(meta.chrom) - 1;
            if(index < 0 || index >= MAX_CHR) continue;

            int start,end;
            double fvalue;

            spliter(line,delimiter,fields,num);

            //get start information
            if(meta.steptype == 1) start = meta.start + section_count * meta.step;
            else start = atoi(fields[0].c_str());
            sprintf(db_record.start,"%010d",start);
            section_count++; //section count

            //get end information
            end = start + meta.span;
            sprintf(db_record.end,"%010d",end);

            strcpy(db_record.sequence,"");
            //get value information
            if(meta.steptype == 1) fvalue = atof(fields[0].c_str());
            if(meta.steptype == 0 && num == 2) fvalue = atof(fields[1].c_str());
            if(fvalue < 0)
            {
                sprintf(db_record.val, "%.2f", -fvalue);
                strcpy(db_record.strand,"-");
            }
            else if(fvalue > 0)
            {
                sprintf(db_record.val, "%.2f", fvalue);
                strcpy(db_record.strand,"+");
            }
            else continue;
        }
        else{
            spliter(line,delimiter,fields,num);
            if(num < 2)
            {
                num = 20;
                char sep = ' ';
                spliter(line,sep,fields,num);
                if(num < 2) continue;
            }
        }

        int col_idx;
        if(in == "wig"){;}
        else if(in == "bed")
        {
            index = chr2id(fields[0]) - 1;
            if(index < 0 || index >= MAX_CHR) continue;

            int value = atoi(fields[1].c_str());
            sprintf(db_record.start,"%010d",value);

            value = atoi(fields[2].c_str());
            sprintf(db_record.end,"%010d",value);

            if(num < 6) strcpy(db_record.strand,"+");
            else{
               strncpy(db_record.strand,fields[5].c_str(),1);
               db_record.strand[1] = '\0';
            }

            strcpy(db_record.val,"1.0");
            strcpy(db_record.sequence,"");
        }
        else if(in == "eland")
        {
            if(num < 3) continue;
            //unique match with 0-2 errors, elandmulti
            if(fields[2] == "1:0:0" || fields[2] == "0:1:0" || fields[2] == "0:0:1")
            {
                char tmpstr[128];
                strncpy(tmpstr,fields[3].c_str(),127);
                MakeUpper(tmpstr);
                string in = tmpstr;
                regex expr("((CHR)?([23][RL]|[IXVY]+|\\d+))");
    
                cmatch what;
                string chrom;
                if(regex_search(in.c_str(), what, expr)) chrom = what.str();
                else continue;
    
                index = chr2id(chrom) - 1;
    
                regex expr1("(\\d+[RF])");
                int start;
                string nstr;
                if(regex_search(in.c_str(), what, expr1)) nstr = what.str();
                else continue;
                start = atoi(nstr.c_str());
    
                strcpy(db_record.sequence, fields[1].c_str());
                int len = strlen(db_record.sequence);
                
                int end = start + len + 1;
                sprintf(db_record.start,"%010d",start);
                sprintf(db_record.end,"%010d",end);
                strcpy(db_record.val, "1.0");
                if(nstr.find("F") >= 0) strcpy(db_record.strand,"+");
                else strcpy(db_record.strand,"-");
            }
            else if(fields[2] == "U0" || fields[2] == "U1" || fields[2] == "U2" || 
                fields[2] == "u0" || fields[2] == "u1" || fields[2] == "u2")
            {
                char tmpstr[128];
                strncpy(tmpstr,fields[6].c_str(),127);
                MakeUpper(tmpstr);
                string in = tmpstr;
                regex expr("((CHR)?([23][RL]|[IXVY]+|\\d+))");

                cmatch what;
                string chrom;
                if(regex_search(in.c_str(), what, expr)) chrom = what.str();
                else continue;

                index = chr2id(chrom) - 1;

                int start = atoi(fields[7].c_str());

                strcpy(db_record.sequence, fields[1].c_str());
                int len = strlen(db_record.sequence);
            
                int end = start + len + 1;
                sprintf(db_record.start,"%010d",start);
                sprintf(db_record.end,"%010d",end);
                strcpy(db_record.val, "1.0");

                if(fields[8] == "F") strcpy(db_record.strand,"+");
                else strcpy(db_record.strand,"-");
            }
            else continue;
        }
        else if(in == "gff")
        {
            if(num < 7) continue;
            if(fields[0].find("fold") >= 0) index = 0;
            else{
                char tmpstr[128];
                strncpy(tmpstr,fields[0].c_str(),127);
                MakeUpper(tmpstr);
                string in = tmpstr;
                regex expr("((CHR)?([23][RL]|[IXVY]+|\\d+))");

                cmatch what;
                string chrom;
                if(regex_search(in.c_str(), what, expr)) chrom = what.str();
                else continue;

                index = chr2id(chrom) - 1;
            }
            int start = atoi(fields[3].c_str());
            int end = atoi(fields[4].c_str());

            strcpy(db_record.sequence, "");
            
            sprintf(db_record.start,"%010d",start);
            sprintf(db_record.end,"%010d",end);
            strcpy(db_record.val, "1.0");

            if(fields[6] == "-") strcpy(db_record.strand,"-");
            else strcpy(db_record.strand,"+");
        }
        else if(in == "elandexport")
        {
            char tmpstr[128];
            strncpy(tmpstr,fields[10].c_str(),127);
            MakeUpper(tmpstr);
            string in = tmpstr;
            regex expr("((CHR)?([23][RL]|[IXVY]+|\\d+))");

            cmatch what;
            string chrom;
            if(regex_search(in.c_str(), what, expr)) chrom = what.str();
            else continue;

            index = chr2id(chrom) - 1;
            if(index < 0 || index >= MAX_CHR) continue;

            int start = atoi(fields[12].c_str());

            strcpy(db_record.sequence, fields[8].c_str());
            int len = strlen(db_record.sequence);
            
            int end = start + len + 1;
            sprintf(db_record.start,"%010d",start);
            sprintf(db_record.end,"%010d",end);
            strcpy(db_record.val, "1.0");

            if(fields[13] == "F") strcpy(db_record.strand,"+");
            else strcpy(db_record.strand,"-");
        }
        else if(in == "mapview")
        {
            if(num < 14) continue;
            char tmpstr[128];
            strncpy(tmpstr,fields[1].c_str(),127);
            MakeUpper(tmpstr);
            string in = tmpstr;
            regex expr("((CHR)?([23][RL]|[IXVY]+|\\d+))");

            cmatch what;
            string chrom;
            if(regex_search(in.c_str(), what, expr)) chrom = what.str();
            else continue;

            index = chr2id(chrom) - 1;
            if(index < 0 || index >= MAX_CHR) continue;

            int start = atoi(fields[2].c_str());
            int len = atoi(fields[13].c_str());
            if(len <= 0 || len > 255) continue;

            int end = start + len + 1;
            sprintf(db_record.start,"%010d",start);
            sprintf(db_record.end,"%010d",end);
            strcpy(db_record.val, "1.0");

            if(fields[3] == "F" || fields[3] == "+") strcpy(db_record.strand,"+");
            else strcpy(db_record.strand,"-");

            if(num > 14) strcpy(db_record.sequence, fields[14].c_str());
            else strcpy(db_record.sequence,"");
        }
        else if(selfDefine)
        {
            //get chromosome information
            col_idx = idx[0]-1;
            if(col_idx < 0 || col_idx > num-1)
            {
                printf("Warning: Can't find column from %d, skipped!\n",col_idx );
                if(!not_end) break;
                else continue;
            }
            index = chr2id(fields[col_idx]) - 1;
            if(index < 0 || index >= MAX_CHR) continue; 
        
            //get start information    
            col_idx = idx[1]-1;
            if(col_idx < 0 || col_idx > num-1)
            {
                printf("Warning: Can't find column from %d, skipped!\n", col_idx);
                continue;
            }
            int start  = atoi(fields[col_idx].c_str());
            if(start <= 0) continue;
        
            //get sequence information
            col_idx = idx[5]-1;
            if(col_idx < 0 || col_idx > num-1) strcpy(db_record.sequence, "");
            else strcpy(db_record.sequence, fields[col_idx].c_str());
    
            int len1 = strlen(db_record.sequence);
            //supose it will be 100 base pair bin if no such information
            if(len1 <= 0) len1 = 100;
            int end;
            //get end information, if lack, calculate from start position
            col_idx = idx[2]-1;
            if(col_idx < 0 || col_idx > num-1) end = start + len1 + 1;
            else end = atoi(fields[col_idx].c_str());
            if(start <= end)
            {
                sprintf(db_record.start,"%010d",start);
                sprintf(db_record.end,"%010d",end);
            }
            else{
                sprintf(db_record.start,"%010d",end);
                sprintf(db_record.end,"%010d",start);
            }
            //get value information
            double score;
            col_idx = idx[4]-1;
            if(col_idx < 0 || col_idx > num-1) score = 1.0;
            else score = atof(fields[col_idx].c_str());
            if(score == 0) continue;
    
            sprintf(db_record.val, "%.2f", fabs(score));
            //get strand information
            col_idx = idx[3]-1;
            if(col_idx < 0 || col_idx > num-1)
            {
                if(score > 0) strcpy(db_record.strand,"+");
                if(score < 0) strcpy(db_record.strand,"-");
            }
            else {
                if(fields[col_idx] == "1" || fields[col_idx] == "F" || fields[col_idx] == "f" || 
                    fields[col_idx] == "+")
                strcpy(db_record.strand,"+");
                else strcpy(db_record.strand,"-");
            }
        }

        memset(&key, 0, sizeof(Dbt));
        memset(&data, 0, sizeof(Dbt));
        //merge identical record, this implementation suppose the input was already sorted by start position
        if(index == old_index && strcmp(db_record.start, old_record.start) == 0 && 
            strcmp(db_record.end, old_record.end) == 0 && 
            strcmp(db_record.strand, old_record.strand) == 0 && 
            strcmp(db_record.sequence, old_record.sequence) == 0)
        {
            double f = atof(db_record.val);
            sum_val += f;
        }
        else{
            //not the first line
            if(old_index >= 0)
            {
                key.set_data(&old_record.start);
                key.set_size(11);

                sprintf(s_value,"%s,%s,%.2f,%s,",old_record.end, old_record.strand, 
                        sum_val, old_record.sequence);
                int len = strlen(s_value);

                data.set_data(s_value);
                data.set_size(len + 1);
                if(total < 20) cout<<s_value<<endl;

                //allow multiple key, but not mutiple data items
                ret = dbp[old_index]->put(NULL, &key, &data,0);
                if(ret == 0) k++;
                if(ret < 0) cout<<"Warning:"<<old_index<<","<<old_record.start<<s_value<<endl;
            }
            else tag_len = atoi(db_record.end) - atoi(db_record.start);

            strcpy(old_record.start, db_record.start);
            //avoid some tags are too long due to unknow errors
            strcpy(old_record.end, db_record.end);
            //int end_pos = atoi(db_record.start) + tag_len;
            //sprintf(old_record.end, "%010d", end_pos);

            strcpy(old_record.strand, db_record.strand);
            strcpy(old_record.sequence, db_record.sequence);
            sum_val = atof(db_record.val);
            old_index = index;
        }

        total++; //total count
        if(total%500000 == 0)
        {
            cout<<"..................."<<total<<" rows were scanned!"<<endl;
        }
        if(!not_end) break;
    }

    //write the last line    
    key.set_data(&old_record.start);
    key.set_size(11);

    sprintf(s_value,"%s,%s,%.2f,%s,",old_record.end, old_record.strand, sum_val, old_record.sequence);
    int len = strlen(s_value);

    data.set_data(s_value);
    data.set_size(len + 1);
    //allow multiple key, but not mutiple data items
    ret = dbp[old_index]->put(NULL, &key, &data,0);
    if(ret == 0) k++;
    if(ret < 0) cout<<"Warning:"<<old_index<<","<<old_record.start<<s_value<<endl;

    cout<<"total "<<total<<" records were scanned,"<<k<<" records were inserted into database!"<<endl;

    for(int i = 0; i < MAX_CHR; i++) dbp[i]->close(0);
    file.Release();    

    string fname1= "chmod 777 " + basedir + "* 2>/dev/null";
    system(fname1.c_str());
    fname1= "chmod 777 "+basedir+" 2>/dev/null";
    system(fname1.c_str());
}
//x_name: database name; o_name: destination file name; basedir: destination directory
int NGSDatabase::Retrieve(const char* x_name, const char* o_name, const char* basedir)
{
    Dbt w_key, w_data;
    int ret;

    Dbt key, data;
    Dbc *p_cur;
    char ekey[10];
    char source[256];

    int len = strlen(x_name);
    if(len < 1) return -1;
    strncpy(source, x_name, len+1);
    if(x_name[len-1] == '/') strncpy(source, x_name, len);
    string dbname = "";
    char* p = rindex(source,'/');
    if(p) dbname = p + 1;

    string fname = o_name;
    string dir = basedir + fname;
    ret = mkdir(dir.c_str(), 0777);
    fname = dir + "/" + fname;
    if(access(fname.c_str(), F_OK) == 0)
    {
        unlink(fname.c_str());
    }

    FileUtils file;

    int count = 0;
    for(int i=0;i<MAX_CHR;i++)
    {
        string chr = id2chr(i+1);
        char db[512];
        sprintf(db,"%s/%s.db%02d",source,dbname.c_str(),i);
       
        cout<<dbname<<": chr"<<i+1<<endl; 
        //open database for read
        Db* dbp = new Db(0,0);
        u_int32_t flags = DB_RDONLY;
        dbp->set_cachesize(0, CACHE_SIZE, 0);

        try{
           ret = dbp->open(NULL, db, NULL, DB_BTREE, flags,0);
        }catch (DbException &e){
           continue; 
        }

        int start = 0;
        sprintf(ekey,"%010d", start);
        memset(&key, 0, sizeof(key));
        key.set_data(ekey);
        key.set_size(11);
        key.set_ulen(11);
        key.set_flags(DB_DBT_USERMEM);

        int sub_cnt = 0;
        string fields[10];
        dbp->cursor(NULL, &p_cur, 0);
        ret = p_cur->get(&key, &data, DB_FIRST);
        if(ret < 0) continue;
        while(1)
        {
            int num = 4;
            char* line = (char*)data.get_data();
            if(!line) break;
            spliter(line, ',', fields, num);
            if(num < 4) continue;
    
            int start,end;
            start = atoi((char*)key.get_data());
            char ch = fields[0].at(0);
            if(ch == '|'){
                string p = fields[0].substr(1);
                end = atoi(p.c_str());
            }
            else end = atoi(fields[0].c_str());

            char s_value[256];
            sprintf(s_value,"%s\t%d\t%d\t%s\t%s\t%s",chr.c_str(),start,end,fields[3].c_str(), fields[2].c_str(),fields[1].c_str());
            ret = file.WriteLine(fname, s_value);

            count++;
            sub_cnt++;
            if(count%500000 == 0)
            {
                cout<<"..................."<<count<<" rows scanned!"<<endl;
            }

            ret = p_cur->get(&key, &data, DB_NEXT);
            if(ret != 0) break;
        }
        if(!p_cur) p_cur->close();
        dbp->close(0);
    }
    file.LastWrite();
    file.Release();
    cout<<"Totally "<<count<<" rows retrieved!"<<endl;
    return count;
}
void NGSDatabase::Query(string db_name,int assembly,int start,int end,char* db_dir)
{
    Dbt key, data;
    Dbc *p_cur;
    char ekey[10];

    string basedir = TrimDir(db_dir);

    Db* dbp = new Db(0,0);
    u_int32_t flags = DB_RDONLY;
    dbp->set_cachesize(0, CACHE_SIZE, 0);

    char postfix[16];
    sprintf(postfix,"%02d",assembly-1);
    string dbname = basedir+db_name+"/"+db_name+".db"+postfix;

    int ret = dbp->open(NULL, dbname.c_str(), NULL, DB_BTREE, flags,0);
    print_error(ret);

    sprintf(ekey,"%010d",start);
    memset(&key, 0, sizeof(key));
    memset(&data, 0, sizeof(Dbt));
    key.set_data(ekey);
    key.set_size(11);
    key.set_ulen(11);
    key.set_flags(DB_DBT_USERMEM);

    dbp->cursor(NULL, &p_cur, 0);

    int count = 0;
    ret = p_cur->get(&key, &data, DB_SET_RANGE);
    while(1)
    {
        if (atoi(ekey) > end) break;
    
        cout<<"start="<<(char*)key.get_data()<<" end="<<(char*)data.get_data()<<endl;
        count++;
        memset(&data, 0, sizeof(Dbt));
        ret = p_cur->get(&key, &data, DB_NEXT);
        if(ret != 0) break;
    }
    cout<<"total "<<count<<" record retrieved!"<<endl;
    
    if(!p_cur) p_cur->close();
    dbp->close(0);
}

void NGSDatabase::Levelone(string db_name,int assembly,int ratio, char* db_dir)
{
    Dbt w_key, w_data;
    u_int32_t w_flags;
    int ret;

    Dbt key, data;
    Dbc *p_cur;
    char ekey[10];
    char postfix[16];

    string basedir = TrimDir(db_dir);
    basedir += db_name + "/";

    //if file exists return, comment it if you want to re-do it
    sprintf(postfix,"%02d-%d",assembly-1,ratio);
    string dbname = basedir + db_name + ".db" + postfix;

     if(access(dbname.c_str(), F_OK) == 0){
        printf("%s exists!\n", dbname.c_str());
        return;
    } 
 
    sprintf(postfix,"%02d",assembly-1);
    dbname = basedir + db_name + ".db" + postfix;

    struct stat stat;
    time_t t;
    time(&t);
    int fd = open(dbname.c_str(), O_RDONLY);
    if(fd < 0)
    {
        printf("%s is inaccessible or not exists!\n", dbname.c_str());
        return;
    }
    fstat(fd,&stat);
    if(t - stat.st_mtime < 300)
    {
        printf("skip %s, maybe still writing!\n", dbname.c_str());
        return;
    }
    close(fd);

    //open database for read
    Db* dbp = new Db(0,0);
    u_int32_t flags = DB_RDONLY;
    dbp->set_cachesize(0, CACHE_SIZE, 0);

    try{
        ret = dbp->open(NULL, dbname.c_str(), NULL, DB_BTREE, flags,0);
    }catch (DbException &e){
        return; 
    }
    if(ret != 0)
    {
        printf("ERROR: %s\n",db_strerror(ret));
        return;
    }

    int start = 0;
    sprintf(ekey,"%010d", start);
    memset(&key, 0, sizeof(key));
    key.set_data(ekey);
    key.set_size(11);
    key.set_ulen(11);
    key.set_flags(DB_DBT_USERMEM);

    int count = 0;
    string fields[10];
    char seq[256];
    int start1,end1;
    string strand;
    double copies;

    int buf_size = MAX_GENOME_SIZE / ratio;
    int cls = 12;
    double * series = (double*)malloc(buf_size*cls*sizeof(double));
    if(!series){
        printf("Can't allocate memory!\n");
        dbp->close(0);
        return;
    }
    memset(series,0,buf_size*cls*sizeof(double));

    int valid_format = 1;
    int group_cnt = 0;
    int cnt = 0;
    dbp->cursor(NULL, &p_cur, 0);
    ret = p_cur->get(&key, &data, DB_SET_RANGE);
    if(ret < 0) return;
    while(1)
    {
        int num = 4;
        char* line = (char*)data.get_data();
        if(!line) break;
        
        spliter(line, ',', fields, num);
        if(num < 4) {
            valid_format = 0;
            break;
        }

        start1 = atoi((char*)key.get_data());
        char ch = fields[0].at(0);
        if(ch == '|'){
            string p = fields[0].substr(1);
            end1 = atoi(p.c_str());
        }
        else end1 = atoi(fields[0].c_str());
        cnt++;

        strand = fields[1];
        copies = atof(fields[2].c_str());

        int cls_id = 0;
        strcpy(seq, fields[3].c_str());
        MakeUpper(seq);
        string group = seq;

        //intensity track, no sequence, no group identifier
        if(group == "")
        {
            group_cnt++;
            if(strand == "+") cls_id = 0;
            if(strand == "-") cls_id = 1;
        }
        //short reads
        else if(group.length() >= 5)
        {
            group_cnt++;
            if(strand == "+") cls_id = 0;
            if(strand == "-") cls_id = 1;
        }
        else
        {
            //DNA methylation
            if(group == "CG")
            {
                if(strand == "+") cls_id = 0;
                if(strand == "-") cls_id = 1;
            } 
            else if(group == "CHG")
            {
                if(strand == "+") cls_id = 2;
                if(strand == "-") cls_id = 3;
            }
            else if(group == "CHH")
            {
                if(strand == "+") cls_id = 4;
                if(strand == "-") cls_id = 5;
            }
            //intensity with group id
            else {
                cls_id = atoi(group.c_str());
                if(strand == "+") cls_id = cls_id * 2;
                if(strand == "-") cls_id = cls_id * 2 + 1;
            }
        }

        if(cls_id < 0 || cls_id > cls) cls_id = 0;

        int x1 = (int)floor(start1*1.0 / ratio);
        int x2 = (int)ceil(end1*1.0 / ratio);
        for(int x = x1; x < x2; x++)
        {
            int gpos = x * ratio;
            double amt = 0;

            if (gpos < start1)
            {
                amt = gpos + ratio - start1;
            }
            else if (gpos + ratio > end1 && gpos < end1)
            {
                amt = end1 - gpos;
            }
            else
            {
                amt = ratio;
            }

            amt *= copies / ratio;

            int idx = gpos / ratio;    
            series[idx*cls + cls_id] += amt;

            count++;
        }
        if(cnt%500000 == 0)
        {
            cout<<"..................."<<cnt<<" records scanned!"<<endl;
        }

        ret = p_cur->get(&key, &data, DB_NEXT);
        if(ret != 0) break;
    }
    cout<<dbname<<":"<<cnt<<" records!"<<endl;
    
    if(!p_cur) p_cur->close();
    dbp->close(0);
    if(!valid_format)
    {
        free(series);
        printf("Invalid data format, ignore this data track!\n");
        return;
    }
    //open database for write
    Db* w_dbp;
    w_dbp = new Db(0,0);
    w_dbp->set_flags(DB_DUPSORT);
    w_dbp->set_bt_compare(compare_int);
    w_dbp->set_errcall(errcall);
    w_dbp->set_cachesize(0, CACHE_SIZE, 0);
    w_flags = DB_CREATE;
        
    char str[4];
    sprintf(str,"%02d-%d", assembly - 1, ratio);
    string _dbname = basedir+"__db."+db_name+".db"+str;
    remove(_dbname.c_str());
    dbname = basedir+db_name+".db"+str;
    remove(dbname.c_str());

    ret = w_dbp->open(NULL, dbname.c_str(), NULL, DB_BTREE, w_flags,0);
    if(ret != 0)
    {
        if(series) free(series);
        printf("ERROR: %s\n",db_strerror(ret));
        return;
    }

    char idx[11];
    char s_value[128];
    count = 0;
    for(int i=0;i<buf_size;i++)
    {
        int skip = 1;
        for(int j = 0;j < cls; j++)
        {
            if(series[i*cls + j] > 0)
            {
                skip = 0;
                break;
            }
        }
        if(skip) continue;

        memset(&key, 0, sizeof(Dbt));
        memset(&data, 0, sizeof(Dbt));
 
        sprintf(idx,"%010d",i*ratio);
        w_key.set_data(&idx);
        w_key.set_size(11);

        strcpy(s_value,"");
        char tmpstr[32];
        int num = cls;
        //reduce redundant data
        if(group_cnt > 20) num = 2;

        for(int j = 0;j < num - 1; j++)
        {
            sprintf(tmpstr,"%.2f,",series[i*cls + j]);
            strcat(s_value, tmpstr);
        }
        sprintf(tmpstr,"%.2f",series[i*cls + num - 1]);
        strcat(s_value, tmpstr);
        int len = strlen(s_value);

        w_data.set_data(s_value);
        w_data.set_size(len + 1);

        //allow multiple key, but not mutiple data items
        ret = w_dbp->put(NULL, &w_key, &w_data,0);
        if(ret < 0) cout<<"Warning:"<<idx<<","<<s_value<<endl;
        count++;
        if(count%100000 == 0)
        {
            cout<<"..................."<<count<<" records written!"<<endl;
        }
    }

    if(series) free(series);
    w_dbp->close(0);

    cout<<"Total "<<count<<" generated, written to "<<dbname<<endl;
    string fname1= "chmod 777 " + basedir + "/*";
    system(fname1.c_str());
}
void NGSDatabase::LevelizeDB(char* db_dir) 
{
    struct stat stat;
    string basedir = TrimDir(db_dir);

    DIR * dp;
    struct dirent *filename;

    dp = opendir(basedir.c_str());
    if(!dp)
    {
        fprintf(stderr,"open directory error\n");
        return;
    }
    
    int count = 0;
    while((filename=readdir(dp))!=NULL)
    {
        string fname = basedir + filename->d_name;
        string dbname = filename->d_name;
        if(lstat(fname.c_str(), &stat) < 0)
        {
            perror("lstat error");
            break;
        }
        if(!S_ISDIR(stat.st_mode) || dbname == "." || dbname == "..") continue;
        count++;
 
        for(int i = 0; i < MAX_CHR; i++)
        {
            Levelone(dbname, i+1, 100, db_dir);
            Levelone(dbname, i+1, 1000, db_dir);
            Levelone(dbname, i+1, 10000, db_dir);
            Levelone(dbname, i+1, 100000, db_dir);
        }
    }
    closedir(dp);
    printf("Total %d tracks were processed!\n",count);
}
