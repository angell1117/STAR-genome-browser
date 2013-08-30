#ifndef __PROCESSDATA_H
#define __PROCESSDATA_H

#include <string>
#pragma once

class NGSDatabase
{
  public:
    static const int MAX_CHR = 26;
  private:
    int CACHE_SIZE;
    int MAX_GENOME_SIZE;
    char delimiter;
  public:
    NGSDatabase();
    NGSDatabase(int);
    ~NGSDatabase();
  public:
    void List(char* dir);
    int GetCount(string db_name,char* db_dir);
    void DropTable(string db_name,char* db_dir);
    void DropDB(char* db_dir);
    void Deposit(string fname,string db_name, string format, const char* db_dir);
    int Retrieve(const char* x_name, const char* o_name, const char* basedir);
    void Query(string db_name,int assembly,int start,int end,char* db_dir);
    void LevelizeDB(char* db_dir);

  private:
    void print_error(int ret);
    void GetMeta(char*line,_meta& meta);
    string TrimDir(char* dir);
    int Count(string db_name);
    void Levelone(string db_name,int assembly,int ratio, char* db_dir);    
};
struct _db_record{
    char start[10+1];
    char end[10+1];
    char strand[1+1];
    char val[32];
    char sequence[256];
};

#endif
