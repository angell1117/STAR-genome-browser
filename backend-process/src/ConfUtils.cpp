#include <cstdio>
#include <iostream>
#include <cstring>
#include <cstdlib>
#include "ConfUtils.h"

#include <fcntl.h>
#include <unistd.h>
#include <sys/stat.h>
#include <sys/types.h>
#include "utils.h"

#ifdef _DEBUG
#undef THIS_FILE
static char THIS_FILE[]=__FILE__;
#define new DEBUG_NEW
#endif

//////////////////////////////////////////////////////////////////////
// Construction/Destruction
//////////////////////////////////////////////////////////////////////
ConfUtils::ConfUtils()
{
}
ConfUtils::~ConfUtils()
{
}
int ConfUtils::GetPara(char* line,const char* para,char* value)
{
   if(line == NULL) return 0;

   Trim(line);
   MakeLower(line);

   char* p =strstr(line,para);
   if(!p) return 0;

   char* l = strstr(p,"=");
   if(!l) return 0;

   p = l + 1;

   int i = 0;
   while(isspace(*(p+i)) && (*(p+i) != '\0')) i++;

   int j = 0;
   while((!isspace(*(p+i+j))) && (*(p+i+j) != '\0')) j++;
   if(j > 0) memcpy(value,p+i,j);
   value[j] = '\0';

   return 1;
}
int ConfUtils::GetConfString(string fname,const char* para,char* value)
{
   char line[1024];

        FILE* fp = fopen(fname.c_str(), "r");
        if(!fp) return 0;

   while(!feof(fp))
   {
      fgets(line,1024,fp);
      Trim(line);
      MakeLower(line);

      //strip comment
      char* comment = strstr(line,"#");
      if(comment)
      {
         int pos = comment - line;
         line[pos] = 0;
      }
      
      char* p =strstr(line,para);
      if(!p) continue; //continue find in next line

      char* l = strstr(p,"=");
      if(l == NULL) 
      {
         fclose(fp);
         return 0;
      }   
      //get the value
      strcpy(value,l+1);
      Trim(value);
      fclose(fp);
      return 1;
   }
   fclose(fp);
   return 0;
}
