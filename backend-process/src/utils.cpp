#include <cstdio>
#include <cstring>
#include <cstdlib>
#include <iostream>

#include <fcntl.h>
#include <time.h>
#include <unistd.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <assert.h>
#include "utils.h"

#ifdef _DEBUG
#undef THIS_FILE
static char THIS_FILE[]=__FILE__;
#define new DEBUG_NEW
#endif

//////////////////////////////////////////////////////////////////////
// Construction/Destruction
//////////////////////////////////////////////////////////////////////

void WriteLog(const char* log_dir, const char* content)
{
        char outstr[200];
        time_t t;
        struct tm *tmp;
  
        if(!log_dir) return;
        t = time(NULL);
        tmp = localtime(&t);
        if(tmp == NULL)
        {
            perror("localtime");
            return;
        }

        if(strftime(outstr,sizeof(outstr),"%Y%m%d",tmp) == 0)
        {
            fprintf(stderr, "strftime returned 0");
            return;
        }
        char timenow[128];

        strftime(timenow,sizeof(timenow),"%T",tmp);

        char fname[256];
        sprintf(fname,"%s/log%s",log_dir,outstr);

        FILE* fp;
        fp = fopen(fname,"a+");
        if(!fp) return;

        fprintf(fp,"[%s:%s]: %s\n",timenow,__FILE__,content);
        fclose(fp);
}

int cmp(const void *a, const void *b)
{
    int ret = strcmp(((Ref*)a)->field[0], ((Ref*)b)->field[0]);
    if(ret != 0) return ret;
    else
    {
        int a1 = atoi(((Ref*)a)->field[1]);
        int b1 = atoi(((Ref*)b)->field[1]);
        if(a1 > b1) return 1;
        if(a1 < b1) return -1;
        return 0;
    }
}
int cmp_int(const void *a, const void *b)
{
    int a_int = *((int*)a);
    int b_int = *((int*)b);
    if(a_int > b_int) return 1;
    if(a_int < b_int) return -1;
    return 0;
}
int GetFieldsCount(const char* line,char delimiter)
{
    int count = 0;
    int i = 0;
    while(*(line+i) != '\0')
    {
       if(*(line+i) == delimiter) count++;
       i++;
    }
    if(count > 0) count++;

    return count;
}
void spliter(const char* line,char delimiter,string* rt,int& cnt)
{
   char field[128];
   char ch;

   int i = 0;
   int pos = 0;
   int count = 0;
   field[0] ='\0';

   while((ch=*(line+i)) != '\0')
   {
      //find the column
      if(ch == delimiter)
      {
         field[pos] = 0;
         pos = 0;
         i++;

         Trim(field);
         if(count < cnt)
         {
            *(rt+count) = field;
            count++;
         }
         if(count == cnt) break;
      }
      else
      {
         if(pos < 127)
         {
            field[pos] = ch;
            pos++;
         }
         i++;
      }
    }
    field[pos] = 0;
    Trim(field);
    if(count < cnt && field[0] != '\0') 
    {
        *(rt+count) = field;
        count++;
    }
    cnt = count;
}
int FileLength(string fname)
{
  int fd;
  struct stat stat;
  
  fd = open(fname.c_str(), O_RDONLY);
  if(fd < 0) return -1;

  return((fstat(fd,&stat) < 0) ? -1 : stat.st_size);
}
char* MakeUpper(char* str)
{
    if(!str) return NULL;

    int i = 0;
    char ch;
        while((ch=*(str+i)) != '\0')
    {
        *(str+i) = toupper(ch);
        i++;
    }
    return str;
}
char* MakeLower(char* str)
{
    if(!str) return NULL;

    int i = 0;
    char ch;
        while((ch=*(str+i)) != '\0')
    {
        *(str+i) = tolower(ch);
        i++;
    }
    return str;
}
char* TrimLeft(char* str)
{
    if(!str) return NULL;

    int i = 0;
    char ch;
        while((ch=*(str+i)) != '\0')
    {
        if(!ISSPACE(ch)) break;
        i++;
    }

    if(i > 0)
    {
        char *p = str+i;
        i = 0;
        while(*(p+i) != '\0') 
        {
            *(str+i) = *(p+i);
            i++;
        }
        *(str+i) = '\0';
    }
    return str;
} 
char* TrimRight(char* str)
{
    if(!str) return NULL;

    int len = strlen(str);
    int i = len-1;
    while(i>=0)
    {
        char ch = *(str+i);
        if(!ISSPACE(ch)) break;
        else
        {
            *(str+i) = '\0';
            i--;
        }
    }
    return str;
}
char* Trim(char* str)
{
    TrimRight(str);
    TrimLeft(str);
    return str;
}
char* TrimZero(char* str, int size, double f, int p)
{
    if(!str || size <= 0 || p < 0) return str;

    char buf[128];
    char format[32];
    sprintf(format, "%%.%df", p);
    sprintf(buf, format, f);

    int len = strlen(buf) - 1;
    if(p > 0)
    {
        while(buf[len--] == '0') buf[len + 1] = 0;
        if(buf[len + 1] == '.')
        { 
            buf[len + 1] = 0;
            len--;
        }
        memcpy(str, buf, len + 3);
    }
    else memcpy(str, buf, len + 2);
    return str;
}
string mybasename(char* path)
{
    if(!path) return "";
    string basename = "";

    char* p = rindex(path,'/');
    if(p) basename = p + 1;
    return basename;
}
int chr2id(string chr)
{
    int index;
    char tmpstr[32];
    char chrome[32];
    
    strncpy(tmpstr, chr.c_str(), 16);
    MakeLower(tmpstr);
    if(strncmp(tmpstr,"chr",3) == 0)
        strcpy(chrome, tmpstr+3);
    else if(strncmp(tmpstr,"chromosome_",11) == 0)
        strcpy(chrome, tmpstr+11);
    else strcpy(chrome, tmpstr);

    if(strlen(chrome) <= 0) return -1;

    if(chrome[0] == 'y') index = 24;
    else if(chrome[0] == 'm') index = 25;
    else if(chrome[0] == 'l') index = 26;
    //for Drosophila melanogaster chromosomes
    else if(strncmp(chrome,"2l",2) == 0) index = 4;
    else if(strncmp(chrome,"2r",2) == 0) index = 5;
    else if(strncmp(chrome,"3l",2) == 0) index = 6;
    else if(strncmp(chrome,"3r",2) == 0) index = 7;
    else if(strcmp(chrome,"i") == 0) index = 1;
    else if(strcmp(chrome,"ii") == 0) index = 2;
    else if(strcmp(chrome,"iii") == 0) index = 3;
    else if(strcmp(chrome,"iv") == 0) index = 4;
    else if(strcmp(chrome,"v") == 0) index = 5;
    else if(strcmp(chrome,"vi") == 0) index = 6;
    else if(strcmp(chrome,"vii") == 0) index = 7;
    else if(strcmp(chrome,"viii") == 0) index = 8;
    else if(strcmp(chrome,"ix") == 0) index = 9;
    else if(strcmp(chrome,"xi") == 0) index = 11;
    else if(strcmp(chrome,"xii") == 0) index = 12;
    else if(strcmp(chrome,"xiii") == 0) index = 13;
    else if(strcmp(chrome,"xiv") == 0) index = 14;
    else if(strcmp(chrome,"xv") == 0) index = 15;
    else if(strcmp(chrome,"xvi") == 0) index = 16;
    else if(chrome[0] == 'x') index = 23;
    else index = atoi(chrome);

    return index;
}

string id2chr(int id)
{
    char chrom[16];
    assert(id >= 0);
    if(id < 23) sprintf(chrom,"chr%d",id);
    else if(id == 23) strcpy(chrom,"chrX");
    else if(id == 24) strcpy(chrom,"chrY");
    else if(id == 25) strcpy(chrom,"chrM");
    else strcpy(chrom,"chrL");
    string str = chrom;
    return str;
}
