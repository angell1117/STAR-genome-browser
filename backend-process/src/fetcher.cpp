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
#include "fetcher.h"
#include "db_cxx.h"
#include "zlib.h"

#define MAX 26

using namespace std;
using namespace boost;

const int db_buffer_size = 256 * 1048576;

struct _intensity{
    double x;
    double pos;
    double neg;
};
struct _sequence{
    int id;
    int start;
    int length;
    int count;
    int copies;
    char seq[256];
};
struct _loc{
  char table[128];
  char tracktype[128];
  char action[128];
  char assembly[64];
  int left;
  int right;
  int bases;
  int pixels;
};
int lowercase(const char *s) {
  return tolower(* (const unsigned char *) s);
}

int mg_strncasecmp(const char *s1, const char *s2, size_t len) {
  int diff = 0;

  if (len > 0)
    do {
      diff = lowercase(s1++) - lowercase(s2++);
    } while (diff == 0 && s1[-1] != '\0' && --len > 0);

  return diff;
}
// URL-decode input buffer into destination buffer.
// 0-terminate the destination buffer. Return the length of decoded data.
// form-url-encoded data differs from URI encoding in a way that it
// uses '+' as character for space, see RFC 1866 section 8.2.1
// http://ftp.ics.uci.edu/pub/ietf/html/rfc1866.txt
size_t url_decode(const char *src, size_t src_len, char *dst,
                         size_t dst_len, int is_form_url_encoded) {
  size_t i, j;
  int a, b;
#define HEXTOI(x) (isdigit(x) ? x - '0' : x - 'W')

  for (i = j = 0; i < src_len && j < dst_len - 1; i++, j++) {
    if (src[i] == '%' &&
        isxdigit(* (const unsigned char *) (src + i + 1)) &&
        isxdigit(* (const unsigned char *) (src + i + 2))) {
      a = tolower(* (const unsigned char *) (src + i + 1));
      b = tolower(* (const unsigned char *) (src + i + 2));
      dst[j] = (char) ((HEXTOI(a) << 4) | HEXTOI(b));
      i += 2;
    } else if (is_form_url_encoded && src[i] == '+') {
      dst[j] = ' ';
    } else {
      dst[j] = src[i];
    }
  }

  dst[j] = '\0'; /* Null-terminate the destination */

  return j;
}

// Scan given buffer and fetch the value of the given variable.
// It can be specified in query string, or in the POST data.
// Return NULL if the variable not found, or allocated 0-terminated value.
// It is caller's responsibility to free the returned value.
int mg_get_var(const char *buf, const char *name,
               char *dst, size_t dst_len) {
  const char *p, *e, *s;
  size_t name_len, len;

  name_len = strlen(name);
  e = buf + strlen(buf);
  len = -1;
  dst[0] = '\0';

  // buf is "var1=val1&var2=val2...". Find variable first
  for (p = buf; p != NULL && p + name_len < e; p++) {
    if ((p == buf || p[-1] == '&') && p[name_len] == '=' &&
        !mg_strncasecmp(name, p, name_len)) {

      // Point p to variable value
      p += name_len + 1;
      // Point s to the end of the value
      s = (const char *) memchr(p, '&', (size_t)(e - p));
      if (s == NULL) {
        s = e;
      }
      assert(s >= p);

      // Decode variable into destination buffer
      if ((size_t) (s - p) < dst_len) {
        len = url_decode(p, (size_t)(s - p), dst, dst_len, 1);
      }
      break;
    }
  }

  return len;
}
void print_loc(struct _loc loc)
{
    printf("action=%s, assembly=%s, left=%d, right=%d, bases=%d, pixels=%d\n",
        loc.action, loc.assembly, loc.left, loc.right, loc.bases, loc.pixels);
    return;
};
//This function get POST data from CGI environment
int get_paras(const char* buf, _loc& loc)
{
    char dst[128];
    memset(&loc, 0 , sizeof(struct _loc));
    int ret = mg_get_var(buf, "action", dst, 128);
    if(ret) strncpy(loc.action, dst, 128);
    ret = mg_get_var(buf,"assembly", dst, 128);
    if(ret) strncpy(loc.assembly, dst, 64);
    ret = mg_get_var(buf, "table", dst, 128);
    if(ret) strncpy(loc.table, dst, 128);
    ret = mg_get_var(buf, "tracktype", dst, 128);
    if(ret) strncpy(loc.tracktype, dst, 128);
    ret = mg_get_var(buf, "left", dst, 128);
    if(ret) loc.left = atoi(dst);
    ret = mg_get_var(buf, "right", dst, 128);
    if(ret) loc.right = atoi(dst);
    ret = mg_get_var(buf, "bases", dst, 128);
    if(ret) loc.bases = atoi(dst);
    ret = mg_get_var(buf, "pixels", dst, 128);
    if(ret) loc.pixels = atoi(dst);
    return 1;
};
void print_error(int ret)
{
    if(ret != 0)
    {
        printf("ERROR: %s\n",db_strerror(ret));
        exit(-1);
    }
}
void init_DBT(Dbt * key, Dbt * data)
{
    memset(key, 0, sizeof(Dbt));
    memset(data, 0, sizeof(Dbt));
}
/*
int compare_int(Db *dbp, const Dbt *a, const Dbt *b)
{
    return memcmp(a->get_data(),b->get_data(),10);
} 
*/
//Simple JSON error string
void json_error(const char* message)
{
    char buf[256];
    cout<<"Server: private server\r\n";
    cout<<"Content-Type: text/html\r\n\r\n";
    sprintf(buf,"{\"success\":false,\"message\":%s}", message);
    printf(buf);
    exit(0);
}
int fwritegz()
{
  const char *data = "this is a gzip test from NinGoo.net";
  gzFile fp=gzopen("test_out.gz","wb");
  gzwrite(fp,data,strlen(data));
  gzclose(fp);
  return 1;
}
/* Compress to gzip data */
int gzipcompress(Bytef *data, uLong ndata, Bytef *zdata, uLong *nzdata)
{
  z_stream c_stream;
  int err = 0;
  if(data && ndata > 0)
  {
    c_stream.zalloc = Z_NULL;
    c_stream.zfree = Z_NULL;
    c_stream.opaque = Z_NULL;
    if(deflateInit2(&c_stream, Z_DEFAULT_COMPRESSION, Z_DEFLATED, 
       -MAX_WBITS, 8, Z_DEFAULT_STRATEGY) != Z_OK) return -1;

    c_stream.next_in  = data;
    c_stream.avail_in  = ndata;
    c_stream.next_out = zdata;
    c_stream.avail_out  = *nzdata;
    while (c_stream.avail_in != 0 && c_stream.total_out < *nzdata)
    {
      if(deflate(&c_stream, Z_NO_FLUSH) != Z_OK) return -1;
    }
    if(c_stream.avail_in != 0) return c_stream.avail_in;
    for (;;) {
      if((err = deflate(&c_stream, Z_FINISH)) == Z_STREAM_END) break;
      if(err != Z_OK) return -1;
    }

    if(deflateEnd(&c_stream) != Z_OK) return -1;
    *nzdata = c_stream.total_out;
    return 0;
   }
  return -1;
}
//type: 0-readstrack, 1-methtrack, 2-intensitytrack
void histogram_json(_intensity* series, int total, int type)
{
    if(total <= 0 || !series) return;
    char* buf = new char[8*1024*1024];
    if(!buf) return;

    int group_no = 1;
    if(type == 1) group_no = 3;
    if(type == 2) group_no = 10;

    strcpy(buf,"{\"success\":true,\"data\":{");
    int first_group = 1;
    for(int k = 0; k < group_no; k++)
    {
       char key1[64];    
       char key2[64];    
       char key3[64];
       char tmpstr[128];

       int first = 1;
       for(int i = 0; i < total; i++)
       {
         if(series[i*10 + k].pos == 0 && series[i*10 + k].neg == 0) continue;
         TrimZero(key1, 64, series[i*10 + k].x, 1);
         TrimZero(key2, 64, series[i*10 + k].pos, 1);
         TrimZero(key3, 64, series[i*10 + k].neg, 1);
         if(first){
           if(type == 0) sprintf(tmpstr, "\"read\":[[%s,%s,%s]", key1, key2, key3);
           if(type == 1){
              if(first_group){
                  if(k == 0)  sprintf(tmpstr, "\"CG\":[[%s,%s,%s]", key1, key2, key3);
                  if(k == 1)  sprintf(tmpstr, "\"CHG\":[[%s,%s,%s]", key1, key2, key3);
                  if(k == 2)  sprintf(tmpstr, "\"CHH\":[[%s,%s,%s]", key1, key2, key3);
              }
              else{
                  if(k == 0)  sprintf(tmpstr, ",\"CG\":[[%s,%s,%s]", key1, key2, key3);
                  if(k == 1)  sprintf(tmpstr, ",\"CHG\":[[%s,%s,%s]", key1, key2, key3);
                  if(k == 2)  sprintf(tmpstr, ",\"CHH\":[[%s,%s,%s]", key1, key2, key3);
              }
              first_group = 0;
           }
           if(type == 2){
              if(first_group){
                 if(k == 0)  sprintf(tmpstr, "\"0\":[[%s,%s,%s]", key1, key2, key3);
                 else sprintf(tmpstr, "\"%d\":[[%s,%s,%s]", k, key1, key2, key3);
              }
              else{
                 if(k == 0)  sprintf(tmpstr, ",\"0\":[[%s,%s,%s]", key1, key2, key3);
                 else sprintf(tmpstr, ",\"%d\":[[%s,%s,%s]", k, key1, key2, key3);
              }
              first_group = 0;
           }
           first = 0;
         }
         else sprintf(tmpstr,",[%s,%s,%s]", key1, key2, key3);
         strcat(buf, tmpstr);    
       }
       if(!first) strcat(buf,"]");
    }
    strcat(buf,"}}");

    uLong src_len = uLong(strlen(buf));
    uLong dest_len = uLong((src_len + 12)*1.001 + 1);
    Bytef* compr = new Bytef[dest_len];
    memset(compr, 0, sizeof(Bytef)*dest_len);

    int ret = gzipcompress((Bytef*)buf, src_len, compr, &dest_len);
    if(ret != 0)
    {
      cout<<"Server: private server\r\n";
      cout<<"Content-Type: text/html\r\n\r\n";
      cout<<buf;
    }
    else
    {
      cout<<"Content-Encoding: deflate\r\n";
      cout<<"Server: private server\r\n";
      cout<<"Content-Type: text/html\r\n\r\n";
      cout.write((char*)(compr), (int)dest_len); 
    }
    delete[] buf;
    delete[] compr;
}

void sequence_json(_sequence* watson, int watson_cnt, _sequence* crick, int crick_cnt)
{
    if(!watson & !crick) return;
    char* buf = new char[8*1024*1024];
    if(!buf) return;

    strcpy(buf,"{\"success\":true,\"data\":{\"read\":{");

    int first = 1;
    char tmpstr[512];
    for(int i = 0; i < watson_cnt; i++)
    {
       if(first){
           sprintf(tmpstr, "\"watson\":[[%d,%d,%d,%d,%d,\"%s\"]", watson[i].id, watson[i].start, 
                            watson[i].length, watson[i].count, watson[i].copies, watson[i].seq);
           first = 0;
       }
       else sprintf(tmpstr, ",[%d,%d,%d,%d,%d,\"%s\"]", watson[i].id, watson[i].start, watson[i].length, 
                              watson[i].count, watson[i].copies, watson[i].seq);
       strcat(buf, tmpstr);    
    }
    if(watson_cnt > 0) strcat(buf,"]");

    for(int i = 0; i < crick_cnt; i++)
    {
       if(i == 0){
           if(first) sprintf(tmpstr, "\"crick\":[[%d,%d,%d,%d,%d,\"%s\"]", crick[i].id, crick[i].start, crick[i].length, 
                             crick[i].count, crick[i].copies, crick[i].seq);
           else sprintf(tmpstr, ",\"crick\":[[%d,%d,%d,%d,%d,\"%s\"]", crick[i].id, crick[i].start, crick[i].length, 
                             crick[i].count, crick[i].copies, crick[i].seq);
       }
       else sprintf(tmpstr, ",[%d,%d,%d,%d,%d,\"%s\"]", crick[i].id, crick[i].start, crick[i].length, 
                     crick[i].count, crick[i].copies, crick[i].seq);
       strcat(buf, tmpstr);    
    }
    if(crick_cnt > 0) strcat(buf,"]");

    strcat(buf,"}}}");

    uLong src_len = uLong(strlen(buf));
    uLong dest_len = uLong((src_len + 12)*1.001 + 1);
    Bytef* compr = new Bytef[dest_len];
    memset(compr, 0, sizeof(Bytef)*dest_len);

    int ret = gzipcompress((Bytef*)buf, src_len, compr, &dest_len);
    if(ret != 0)
    {
      cout<<"Server: private server\r\n";
      cout<<"Content-Type: text/html\r\n\r\n";
      cout<<buf;
    }
    else
    {
      cout<<"Content-Encoding: deflate\r\n";
      cout<<"Server: private server\r\n";
      cout<<"Content-Type: text/html\r\n\r\n";
      cout.write((char*)(compr), (int)dest_len); 
    }
    delete[] buf;
    delete[] compr;
}

//type: 0-readstrack, 1-methtrack, 2-intensitytrack
void get_level_data(char* db_dir, struct _loc loc, int type)
{
    Dbt w_key, w_data;
    int ret;

    Dbt key, data;
    Dbc *p_cur;
    char ekey[10];

    Trim(db_dir);
    int len = strlen(db_dir);
    if(len < 1) return;
    if(db_dir[len-1] == '/') db_dir[len-1] = '\0';

    string assembly = loc.assembly;
    int chr_idx = chr2id(assembly);

    int ratio = loc.bases  / loc.pixels;
     if(ratio <= 0){
        json_error("No database found!");
        return;
    } 
    char dbname[512];
    sprintf(dbname,"%s/%s/%s.db%02d-%d", db_dir, loc.table, loc.table, chr_idx-1, ratio);

     if(access(dbname, F_OK) != 0){
        json_error("No database found!");
        return;
    } 
 
    //open database for read
    Db* dbp = new Db(0,0);
    u_int32_t flags = DB_RDONLY;
    dbp->set_cachesize(0, db_buffer_size, 0);

    try{
            ret = dbp->open(NULL, dbname, NULL, DB_BTREE, flags,0);
    }catch (DbException &e){
        json_error("Database failed!");
        return; 
    }
    if(ret != 0)
    {
        json_error("Database failed!");
        return;
    }

    sprintf(ekey,"%010d", loc.left);
    memset(&key, 0, sizeof(key));
    key.set_data(ekey);
    key.set_size(11);
    key.set_ulen(11);
    key.set_flags(DB_DBT_USERMEM);

    string fields[20];

    int total = (int)((loc.right - loc.left) / ratio) + 1;
    _intensity * series = new _intensity[total*10];
    if(!series){
        json_error("Can't allocate memory!\n");
        dbp->close(0);
        return;
    }
    memset(series,0,total*10*sizeof(_intensity));

    dbp->cursor(NULL, &p_cur, 0);
    ret = p_cur->get(&key, &data, DB_SET_RANGE);
    if(ret < 0) return;
    while(1)
    {
        int num = 20;
        char* line = (char*)data.get_data();
        if(!line) break;
        spliter(line, ',', fields, num);
	if(num < 2) continue;

        int start = atoi((char*)key.get_data());
        if(start >= loc.right) break;

        int idx = (start - loc.left) / ratio;
        int groups = num / 2;
        int remain = num % 2;

        for(int i = 0; i < groups; i++)
        {
           series[idx*10 + i].x = start;
           series[idx*10 + i].pos = atof(fields[i*2].c_str());
           series[idx*10 + i].neg = atof(fields[i*2 + 1].c_str());
        }
        if(remain){
           series[idx*10 + groups].x = start;
           series[idx*10 + groups].pos = atof(fields[groups*2 + remain - 1].c_str());
        }

        ret = p_cur->get(&key, &data, DB_NEXT);
        if(ret != 0) break;
    }
    
    if(!p_cur) p_cur->close();
    dbp->close(0);
    histogram_json(series, total, type);
    delete[] series;
}
void get_histogram(char* db_dir, struct _loc loc, int type)
{
    Dbt w_key, w_data;
    int ret;

    Dbt key, data;
    Dbc *p_cur;
    char ekey[10];

    Trim(db_dir);
    int len = strlen(db_dir);
    if(len < 1) return;
    if(db_dir[len-1] == '/') db_dir[len-1] = '\0';

    string assembly = loc.assembly;
    int chr_idx = chr2id(assembly);

    char dbname[512];
    sprintf(dbname,"%s/%s/%s.db%02d", db_dir, loc.table, loc.table, chr_idx-1);

     if(access(dbname, F_OK) != 0){
        json_error("No database found!");
        return;
    } 
 
    //open database for read
    Db* dbp = new Db(0,0);
    u_int32_t flags = DB_RDONLY;
    dbp->set_cachesize(0, db_buffer_size, 0);

    try{
            ret = dbp->open(NULL, dbname, NULL, DB_BTREE, flags,0);
    }catch (DbException &e){
        json_error("Database failed!");
        return; 
    }
    if(ret != 0)
    {
        json_error("Database failed!");
        return;
    }

    sprintf(ekey,"%010d", loc.left);
    memset(&key, 0, sizeof(key));
    key.set_data(ekey);
    key.set_size(11);
    key.set_ulen(11);
    key.set_flags(DB_DBT_USERMEM);

    string fields[10];
    double ratio = loc.bases * 1.0 / loc.pixels;
    int ll = int(loc.left / ratio + 0.5);
    int rr = int(loc.right / ratio + 0.5);

    int total = (int)((loc.right - loc.left) / ratio) + 1;
    _intensity * series = new _intensity[total*10];
    if(!series){
        json_error("Can't allocate memory!\n");
        dbp->close(0);
        return;
    }
    memset(series,0,total*10*sizeof(_intensity));

    dbp->cursor(NULL, &p_cur, 0);
    ret = p_cur->get(&key, &data, DB_SET_RANGE);
    if(ret < 0) return;
    while(1)
    {
        int num = 4;
        char* line = (char*)data.get_data();
        if(!line) break;
        spliter(line, ',', fields, num);
        if(num < 4) continue;

        int start = atoi((char*)key.get_data());
        int end;
        if(fields[0].at(0) == '|')
            end = atoi(fields[0].substr(1).c_str());
        else end = atoi(fields[0].c_str());
        if(start >= loc.right) break;

        string strand = fields[1];
        double copies = atof(fields[2].c_str());
        char group[256];
        int l = fields[3].length();
        strncpy(group, fields[3].c_str(), 255);
        if(l > 0 && group[l-1] == '|') group[l-1] = '\0';
      
        //get the proper group number 
        MakeUpper(group); 
	int cls = 0;

	if(type == 1){
           if(strncmp(group, "CG", 2) == 0) cls = 0;
           if(strncmp(group, "CHG", 3) == 0) cls = 1;
           if(strncmp(group, "CHH", 3) == 0) cls = 2;
        }
	if(type == 2){
	   cls = atoi(group);
           if(cls <= 0) cls = 9;
        }

        int x1 = (int)floor(start*1.0 / ratio);
        int x2 = (int)ceil(end*1.0 / ratio);
        for(int x = x1; x < x2; x++)
        {
            double gpos = x * ratio;
            double amt = 0;

            if (gpos < start)
            {
               amt = gpos + ratio - start;
            }
            else if (gpos + ratio > end && gpos < end)
            {
               amt = end - gpos;
            }
            else
            {
               amt = ratio;
            }

            amt *= copies / ratio;

            int idx = int(gpos / ratio);
            if(idx >= ll && idx < rr)
            {
                series[(idx - ll)*10 + cls].x = gpos;
                if(strand == "-"){
                   series[(idx - ll)*10 + cls].neg += amt;
                }
                else{
                   series[(idx - ll)*10 + cls].pos += amt;
                }
            }
        }

        ret = p_cur->get(&key, &data, DB_NEXT);
        if(ret != 0) break;
    }
    
    if(!p_cur) p_cur->close();
    dbp->close(0);
    histogram_json(series, total, type);
    delete[] series;
}
void get_boxes(char* db_dir, struct _loc loc, int contain_sequence)
{
    Dbt w_key, w_data;
    int ret;

    Dbt key, data;
    Dbc *p_cur;
    char ekey[10];

    Trim(db_dir);
    int len = strlen(db_dir);
    if(len < 1) return;
    if(db_dir[len-1] == '/') db_dir[len-1] = '\0';

    string assembly = loc.assembly;
    int chr_idx = chr2id(assembly);

    char dbname[512];
    sprintf(dbname,"%s/%s/%s.db%02d", db_dir, loc.table, loc.table, chr_idx-1);

     if(access(dbname, F_OK) != 0){
        json_error("No database found!");
        return;
    } 
 
    //open database for read
    Db* dbp = new Db(0,0);
    u_int32_t flags = DB_RDONLY;
    dbp->set_cachesize(0, db_buffer_size, 0);

    try{
            ret = dbp->open(NULL, dbname, NULL, DB_BTREE, flags,0);
    }catch (DbException &e){
        json_error("Database failed!");
        return; 
    }
    if(ret != 0)
    {
        json_error("Database failed!");
        return;
    }

    sprintf(ekey,"%010d", loc.left);
    memset(&key, 0, sizeof(key));
    key.set_data(ekey);
    key.set_size(11);
    key.set_ulen(11);
    key.set_flags(DB_DBT_USERMEM);

    string fields[10];
#define MAX_SEQ 20000
    _sequence * watson = new _sequence[MAX_SEQ];
    if(!watson){
        json_error("Can't allocate memory!\n");
        dbp->close(0);
        return;
    }
    _sequence * crick = new _sequence[MAX_SEQ];
    if(!crick){
        json_error("Can't allocate memory!\n");
        dbp->close(0);
        return;
    }
    memset(watson,0, MAX_SEQ*sizeof(_sequence));
    memset(crick,0, MAX_SEQ*sizeof(_sequence));
    int watson_cnt = 0;
    int crick_cnt = 0;

    dbp->cursor(NULL, &p_cur, 0);
    ret = p_cur->get(&key, &data, DB_SET_RANGE);
    if(ret < 0) return;
    while(1)
    {
        int num = 4;
        char* line = (char*)data.get_data();
        if(!line) break;
        spliter(line, ',', fields, num);
        if(num < 4) continue;

        int start = atoi((char*)key.get_data());
        int end;
        if(fields[0].at(0) == '|')
            end = atoi(fields[0].substr(1).c_str());
        else end = atoi(fields[0].c_str());

        if(start >= loc.right) break;

        string strand = fields[1];
        int copies = atoi(fields[2].c_str());
        char seq[256];
        int l = fields[3].length();
        strncpy(seq, fields[3].c_str(), 255);
        if(l > 0 && seq[l-1] == '|') seq[l-1] = '\0';

        if(strand == "+" && watson_cnt < MAX_SEQ)
        {
           watson[watson_cnt].id = start;
           watson[watson_cnt].start = start;
           watson[watson_cnt].length = end - start;
           watson[watson_cnt].count = 1;
           watson[watson_cnt].copies = copies;
           if(contain_sequence)
              strcpy(watson[watson_cnt].seq, seq);
           else
              strcpy(watson[watson_cnt].seq, "");
           watson_cnt++;
        }
        if(strand == "-" && crick_cnt < MAX_SEQ)
        {
           crick[crick_cnt].id = start;
           crick[crick_cnt].start = start;
           crick[crick_cnt].length = end - start;
           crick[crick_cnt].count = 1;
           crick[crick_cnt].copies = copies;
           if(contain_sequence)
              strcpy(crick[crick_cnt].seq, seq);
           else
              strcpy(crick[crick_cnt].seq, "");
           crick_cnt++;
        }
        ret = p_cur->get(&key, &data, DB_NEXT);
        if(ret != 0) break;
    }
    
    if(!p_cur) p_cur->close();
    dbp->close(0);
    sequence_json(watson, watson_cnt, crick, crick_cnt);
    delete[] watson;
    delete[] crick;
}
int main(int argc, char* argv[]) 
{
  char request[1024];
  struct _loc loc;

  //GET request from client browser
  if(getenv("QUERY_STRING"))
  {
      printf("Content-Type: text/html\r\n\r\n");

      strcpy(request, getenv("QUERY_STRING"));
      get_paras(request, loc);
      if(strcmp(loc.action, "syndicate") == 0)
      {
        char institute[256];
        char engineer[256];
        char service[256];
        strcpy(institute, "{\"name\":\"UCSD\",\"url\":\"http:\\/\\/wanglab.ucsd.edu\\/star\\/browser\",\"logo\":\"\"}");
        strcpy(engineer, "{\"name\":\"Wang Lab\",\"email\":\"star@wanglab.ucsd.edu\"}");
        strcpy(service, "{\"title\":\"\",\"species\":\"Homo Sapiens\",\"access\":\"public\",\"version\":\"\",\"format\":\"\",\"server\":\"\",\"description\":\"\"}");
        printf("{\"success\":true, \"data\":{\"institute\":%s, \"engineer\":%s, \"service\":%s}}", institute, engineer, service);
        return 0;

      }     
    return 0;
  }

  char db_dir[1024];
  ConfUtils conf;
  int ret = conf.GetConfString("fetcher.conf", "db_dir", db_dir);
  if(ret < 0) return 0;

  if(!getenv("CONTENT_LENGTH")) return 0;

  //POST request from client browser
  int n = atoi(getenv("CONTENT_LENGTH"));
  fgets(request, n+1, stdin);
  get_paras(request, loc);
  if(loc.bases <= 0 || loc.pixels <= 0 || loc.right <= 0) return 0;

  string fields[64];
  int dir_num = 64;
  char dbname[512];

  spliter(db_dir, ':', fields, dir_num);
  db_dir[0] = '\0';
  for(int i = 0; i < dir_num; i++)
  {
     strncpy(db_dir, fields[i].c_str(), 512);
     int len = strlen(db_dir);
     if(len < 1) continue;
     if(db_dir[len-1] == '/') db_dir[len-1] = '\0';

     sprintf(dbname,"%s/%s/%s.db00", db_dir, loc.table, loc.table);
     if(access(dbname, F_OK) == 0) break;
     else db_dir[0] = '\0';
  }
  if(db_dir[0] == '\0') return 0;

  //strcpy(loc.table,"UCSD.H1.H4K20me1.YL253");
  if(strcmp(loc.action, "range") == 0)
  {
     char tracktype[128];
     strcpy(tracktype, loc.tracktype);
     MakeLower(tracktype);
     int type = 0;
     if(strcmp(tracktype, "readstrack") == 0) type = 0;
     if(strcmp(tracktype, "methtrack") == 0) type = 1;
     if(strcmp(tracktype, "intensitytrack") == 0) type = 2;

     Trim(db_dir);
     int len = strlen(db_dir);
     if(len < 1) return 0;
     if(db_dir[len-1] == '/') db_dir[len-1] = '\0';

     string assembly = loc.assembly;
     int chr_idx = chr2id(assembly);

     double ratio = loc.bases * 1.0 / loc.pixels;
     char dbname[512];
     sprintf(dbname,"%s/%s/%s.db%02d-%d", db_dir, loc.table, loc.table, chr_idx-1, (int)ratio);
     if(access(dbname, F_OK) == 0) get_level_data(db_dir, loc, type);
     else{
        if(type == 0)
        {
           if(ratio >= 5.0) get_histogram(db_dir, loc, type);
           else if(ratio >= 0.2) get_boxes(db_dir, loc, 0);
           else get_boxes(db_dir, loc, 1);
        }
        else get_histogram(db_dir, loc, type);
     }
  }
     return 0;
} 
