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
#include "ngsproc.h"

using namespace std;
using namespace boost;

void print_usage(FILE*stream,const char* prog)
{
    fprintf(stream,"Usage:%s [options ....]\n", prog);
    fprintf(stream,"-h --help Display this usage information\n"
            "-a --action\t database operation, in [create|query|drop|level|count]\n"
            "-d --db-dir\t Database directory\n"
            "-t --table\t Database table you want to operate\n"
            "-i --input\t Input data filename, only for 'create'\n"
            "-f --format\t Data format, in [BED|WIG|BOWTIE|BOWTIESAM|SAM|ELAND|ELANDMULTI|ELANDEXPORT|GFF|MAPVIEW]\n"
            "-s --start\t Start position, only for 'query', default 0\n"
            "-e --end\t End position, only for 'query', default 100000\n"
            "-c --chrom\t Chromsome index, default 1\n"
        );
}
int main(int argc, char* argv[]) 
{
    if(argc < 2)
    {
        print_usage(stdout,argv[0]);
        return 0;
    }

    NGSDatabase ngs;
    int next_option;
    const char* const short_options = "ha:d:t:i:f:s:e:c:";
    const struct option long_options[]={
        {"help",0,NULL,'h'},
        {"action",1,NULL,'a'},
        {"dir",1,NULL,'d'},
        {"table",1,NULL,'t'},
        {"input",1,NULL,'i'},
        {"format",1,NULL,'f'},
        {"start",1,NULL,'s'},
        {"end",1,NULL,'e'},
        {"chrom",1,NULL,'c'},
        {NULL,0,NULL,0}
    };

    char action[128];
    char db_dir[512];
    char filename[256];
    char dbname[256];
    char format[128];
    db_dir[0] = filename[0] = dbname[0] = format[0] = '\0';

    int start = 0;
    int end = 1000000;
    int chr = 1;
    int verbose = 0;

    do{
        next_option = getopt_long(argc,argv,short_options,long_options,NULL);
        switch(next_option)
        {
            case 'h': 
                print_usage(stdout,argv[0]);
                return 0;
            case 'a': 
                strncpy(action, optarg, 128);
                MakeLower(action);
                verbose++;
                break;
            case 'd': 
                strncpy(db_dir, optarg, 512);
                break;
            case 't': 
                strncpy(dbname, optarg, 256);
                break;
            case 'i': 
                strncpy(filename, optarg, 256);
                break;
            case 'f': 
                strncpy(format, optarg, 128);
                MakeLower(format);
                break;
            case 's': 
                start = atoi(optarg);
                if(start < 0) start = 0;
                break;
            case 'e': 
                end = atoi(optarg);
                if(end < 0) end = 0;
                break;
            case 'c': 
                chr = atoi(optarg);
                if(chr < 0 || chr > NGSDatabase::MAX_CHR)
                {
                    printf("Chromsome index beyond [0, %d] \n", NGSDatabase::MAX_CHR);
                    return 0;
                }
                break;
            default: 
                break;
        }
    }while(next_option !=-1);

    if(verbose <= 0)
    {
        printf("%s\tYou must specify '-a or --action' parameter!\n",argv[0]);
        print_usage(stdout,argv[0]);
        return 0;
    }

    string op = action;
    if(op != "create" && op != "query" && op != "drop" && op != "level" && op != "count")
    {
        printf("Specify action in [create|query|drop|level|count]!\n");
        print_usage(stdout,argv[0]);
        return 0;
    }
    
    if(op == "create")
    {
        if(db_dir[0] == 0 || filename[0] ==0 || dbname[0] == 0 || format[0] == 0)
        {
            printf("Please specify -d -t -i -f parameters!\n");
            print_usage(stdout,argv[0]);
            return 0;
        }
        printf("DB_dir = %s\n", db_dir);
        printf("Table name = %s\n", dbname);
        printf("input file = %s\n", filename);
        printf("File format = %s\n", format);
    }
    if(op == "query")
    {
        if(db_dir[0] == 0 ||  dbname[0] == 0)
        {
            printf("Please specify -d -t parameters!\n");
            print_usage(stdout,argv[0]);
            return 0;
        }
        printf("DB_dir = %s\n", db_dir);
        printf("Table name = %s\n", dbname);
        printf("Start = %d\n", start);
        printf("End = %d\n", end);
        printf("Chr index = %d\n", chr);
    }
    if(op == "drop")
    {
        if(db_dir[0] == 0)
        {
            printf("Please specify -d parameter!\n");
            print_usage(stdout,argv[0]);
            return 0;
        }
        printf("DB_dir = %s\n", db_dir);
    }
    if(op == "level")
    {
        if(db_dir[0] == 0)
        {
            printf("Please specify -d parameter!\n");
            print_usage(stdout,argv[0]);
            return 0;
        }
        printf("DB_dir = %s\n", db_dir);
    }
    if(op == "count")
    {
        if(db_dir[0] == 0 ||  dbname[0] == 0)
        {
            printf("Please specify -d -t parameters!\n");
            print_usage(stdout,argv[0]);
            return 0;
        }
        printf("DB_dir = %s\n", db_dir);
        printf("Table name = %s\n", dbname);
    }


    struct timeval tpstart,tpend;
    float timeuse;
    gettimeofday(&tpstart,NULL); 

    cout<<"Processing..."<<endl;

    if(op == "create") ngs.Deposit(filename, dbname, format, db_dir);
    if(op == "query") ngs.Query(dbname, chr, start, end, db_dir);
    if(op == "drop") ngs.DropDB(db_dir);
    if(op == "level") ngs.LevelizeDB(db_dir);
    if(op == "count") ngs.GetCount(dbname, db_dir);

    gettimeofday(&tpend,NULL);
    timeuse = 1000000*(tpend.tv_sec - tpstart.tv_sec) + tpend.tv_usec - tpstart.tv_usec;
    timeuse /= 1000000;

    cout<<".....finished! Total time:"<<timeuse<<" seconds!"<<endl;
    return 0;
} 
