#include <cstdio>
#include <iostream>
#include <cstring>
#include <cstdlib>

#include <fcntl.h>
#include <unistd.h>
#include <sys/stat.h>
#include <sys/types.h>

#include "utils.h"
#include "FileUtils.h"

#ifdef _DEBUG
#undef THIS_FILE
static char THIS_FILE[]=__FILE__;
#define new DEBUG_NEW
#endif

//////////////////////////////////////////////////////////////////////
// Construction/Destruction
//////////////////////////////////////////////////////////////////////
FileUtils::FileUtils()
{
   fp = NULL;
   out_fp = NULL;      
   g_buf_size = 4*1024*1024;
   real_buf_size = 0;
   g_fp_pos = 0;
   g_buf_pos = 0;
   out_buf_pos = 0;
   g_buffer = NULL;
   out_buffer = NULL;
}
FileUtils::FileUtils(int buf_size)
{
   fp = NULL;
   out_fp = NULL;
   if(buf_size < 4096) g_buf_size = 4096;
   else g_buf_size = buf_size;

   real_buf_size = 0;
   g_fp_pos = 0;
   g_buf_pos = 0;
   out_buf_pos = 0;
   g_buffer = NULL;
   out_buffer = NULL;
}
FileUtils::~FileUtils()
{
   if(g_buffer) delete[] g_buffer;
   if(out_buffer) delete[] out_buffer;
}
int FileUtils::PrintLines(string fname)
{
   char line[1024];

   int ret;

   ret = OpenFileForRead(fname);
   if(!ret) return -1;

   ret = AllocBuffer();
   if(!ret) return -1;
   ret = FirstRead();
   if(!ret) 
   {
      Release();
      return -1;
   }
   while(1)
   {
      ret = GetLine(line,1024);
      
      Trim(line);
      MakeLower(line);
      cout<<"|"<<line<<"|"<<endl;
      if(ret) break;
   }
   Release();
   return 1;
}
string FileUtils::GetField(const char* line,int index, char delimiter)
{
   string rtstr="";

   int count = 0, i = 0;
   int pos;
   while(*(line+i)!='\0')
   {
      //find the column
      if(i == 0 || *(line+i) == delimiter)
      {
         count++;
         if(count == index)
         {
            if(i == 0) pos = 0;
            if(*(line+i) == delimiter) pos = i+1;
            
            while(*(line+pos) != delimiter && *(line+pos) != '\0')
            {
               rtstr += tolower(*(line+pos));
               pos++;
            }
            break;
         }
      }
      i++;
   }
   return rtstr;
}
string FileUtils::GetField(string line,int index, char delimiter)
{
   string rtstr="";
   int len = line.length();

   int count = 0, i = 0;
   int pos;
   while(i < len)
   {
      char ch = line.at(i);
      //find the column
      if(i == 0 || ch == delimiter)
      {
         count++;
         if(count == index)
         {
            if(i == 0) pos = 0;
            if(ch == delimiter) pos = i+1;
            int j;
            for(j=pos;j<len;j++)
            {
               if(line.at(j) == delimiter) break;
               rtstr += tolower(line.at(j));
            }
            break;
         }
      }
      i++;
   }
   return rtstr;
}
int FileUtils::GetFieldsNum(string line,char delimiter)
{
   int count = 0;
   int i = 0;
   while(line.at(i) != '\0')
   {
      char ch = line.at(i);
      if(ch == delimiter) count++;
      i++;
   }
   if(count > 0) count++;

   return count;
}
bool FileUtils::OpenFileForRead(string fname)
{
   fp = fopen(fname.c_str(), "r");
   if(!fp) return 0;

   return 1;
}
bool FileUtils::OpenFileForWrite(string fname)
{
   string fname1 = fname+".out";
   out_fp = fopen(fname1.c_str(), "w+");
   if(!out_fp) return 0;

   return 1;
}
bool FileUtils::FirstRead()
{
   real_buf_size = fread(g_buffer, sizeof(char), g_buf_size, fp);
   if(real_buf_size < 0) return 0;

   return 1;
}
bool FileUtils::AllocBuffer()
{
   g_buffer = new char[g_buf_size];
   out_buffer = new char[g_buf_size];

   if(g_buffer && out_buffer) return 1;
   else return 0;
}
void FileUtils::Release()
{
   if(fp) fclose(fp);
   if(out_fp) fclose(out_fp);
   if(g_buffer)delete[] g_buffer;
   if(out_buffer) delete[] out_buffer;
   fp = out_fp = NULL;
   g_buffer = out_buffer = NULL;
   real_buf_size = 0;
   g_fp_pos = 0;
   g_buf_pos = 0;
   out_buf_pos = 0;
}
int FileUtils::CountLines(string fname)
{
   FILE* fp = fopen(fname.c_str(), "r");
   if(!fp) return -1;

   char* buffer=NULL;
   int buf_size=1024*1024;

   buffer = new char[buf_size];
   if(!buffer) return -1;

   fseek(fp,0,SEEK_SET);
   int sizeread = -1;

   int count=0;
   while(1)
   {
      sizeread = fread(buffer, sizeof(char), buf_size, fp);

      int old_pos = 0;
      for(int i=0;i<sizeread;i++)
      {
         if(*(buffer+i) == '\n') 
         {
            old_pos = i+1;
            count++;
         }
      }
      //EOF or error occurs
      if(feof(fp)) 
      {
         if(old_pos < sizeread) count++;
         break;
      }
   }
   fclose(fp);
   delete[] buffer;

   return count;
}
int FileUtils::GetLine(char* line, int line_size)
{
   int i;
   int count = 0;

   if(line == NULL) return 0;
   while(1)
   {
      for(i=g_buf_pos;i<real_buf_size;i++)
      {
         char ch = *(g_buffer+i);
         if(ch == '\n')
         {
            if(count >= line_size) count = line_size - 1;
            *(line+count) = '\0';
            g_buf_pos = i+1;
            return 1;
         }
         else
         {
            if(count < line_size-1) *(line+count) = ch;
            count++;
         }
      }
      //eof of the file
      if(feof(fp))
      {
         if(count < line_size-1) *(line+count) = '\0';
         else  *(line+line_size-1) = '\0';
         return 0;
      }

      //not eof of file and did not get a line,refill the buffer and continue fill line buffer
      g_buf_pos = 0;
      real_buf_size = fread(g_buffer, sizeof(char), g_buf_size, fp);
   }
   return 1;
}
void FileUtils::SeekToBegin()
{
   fseek(fp,0,SEEK_SET);
}
int FileUtils::WriteLine(char* line)
{
   if(!line) return -1;
   int len = strlen(line);

   //buffer is full
   if(out_buf_pos+len+1 > g_buf_size)
   {
      //write to file
      fwrite(out_buffer, sizeof(char), out_buf_pos, out_fp);
      out_buf_pos = 0;
   }

   memcpy(out_buffer+out_buf_pos,line,len);
   out_buf_pos += len;
   *(out_buffer + out_buf_pos) = '\n';
   out_buf_pos++;
   return 1;
}
bool FileUtils::WriteLine(string fname,char* line)
{
   if(out_fp == NULL)
   {
      out_fp = fopen(fname.c_str(), "w+");
      if(!out_fp) return 0;
           out_buffer = new char[g_buf_size];
           if(!out_buffer) 
      {
         fclose(out_fp);
         out_fp = NULL;
         return 0;
      }
   }

   WriteLine(line);
   return 1;
}
bool FileUtils::WriteLine(string fname,char* line,const char* tag)
{
   if(out_fp == NULL)
   {
      out_fp = fopen(fname.c_str(), tag);
      if(!out_fp) return 0;
           out_buffer = new char[g_buf_size];
           if(!out_buffer) 
      {
         fclose(out_fp);
         out_fp = NULL;
         return 0;
      }
   }

   WriteLine(line);
   return 1;
}
bool FileUtils::LastWrite()
{
   if(out_buf_pos > 1) fwrite(out_buffer, sizeof(char), out_buf_pos-1, out_fp);
   out_buf_pos = 0;

   return 1;
}
void FileUtils::qsortfile(string m_fname1,char delimiter) 
{
   // TODO: Add your control notification handler code here
   char line[10240];
   Ref* refseq=NULL;

   if(m_fname1 == "")
   {
      cout<<"please input a file for sort!"<<endl;
      return;
   }


   int ret;
   int total = CountLines(m_fname1);

   ret = OpenFileForRead(m_fname1);
   if(!ret) return;
   ret = OpenFileForWrite(m_fname1);
   if(!ret) return;

   ret = AllocBuffer();
   if(!ret) return;
   ret = FirstRead();
   if(!ret) 
   {
      Release();
      return;
   }

   refseq = new Ref[50000];
   if(!refseq)
   {
      Release();
      return;
   }

   cout<<"Begin to process:"<<endl;
   int k = 0;
   int count = 0;
   while(k < total)
   {
      ret = GetLine(line,10240);

      string buffer = line;
      count = GetFieldsNum(buffer,delimiter);
   
      for(int j=0;j<count;j++)
      {
         string field = GetField(buffer,j+1,delimiter);

         strcpy(refseq[k].field[j],field.c_str());
      }

      cout<<"...";
      k++;
   }
   
   qsort(refseq,k,sizeof(Ref),cmp); 

   char line1[1024];
   line1[0] = '\0';

   //write the sorted resuts to file
   for(int j=0; j<k;j++)
   {
      strcpy(line1,"");
      for(int kk=0;kk<count-1;kk++)
      {
         strcat(line1,refseq[j].field[kk]);
         strcat(line1,"\t");
      }
      strcat(line1,refseq[j].field[count-1]);
      WriteLine(line1);
   }
   LastWrite();

   delete[] refseq;
   Release();   
}
