#ifndef __UTILS_H
#define __UTILS_H

#include <string>
using namespace std;
#pragma once

#define ISSPACE(x) ((x)==' '||(x)=='\r'||(x)=='\n'||(x)=='\f'||(x)=='\b'||(x)=='\t')

struct Ref
{
	char field[20][32];
};
struct _meta
{
	char chrom[16];
	int start;
	int step;
	int span;
	int steptype;
};

extern int cmp(const void *a, const void *b);
extern int cmp_int(const void *a, const void *b);
int FileLength(string fname);
char* MakeLower(char* str);
char* MakeUpper(char* str);
char* TrimLeft(char* str);
char* TrimRight(char* str);
char* Trim(char* str);
char* TrimZero(char* str, int size, double f, int p);
string mybasename(char* path);
void spliter(const char* line,char delimiter,string* rt,int& cnt);
int GetFieldsCount(const char* line,char delimiter);
int chr2id(string chr);
string id2chr(int id);
void WriteLog(const char* log_dir, const char* content);
#endif
