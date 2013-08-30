#ifndef __FILEUTILS_H
#define __FILEUTILS_H

#include <string>
using namespace std;
#pragma once

class FileUtils  
{
public:
	FileUtils();
	FileUtils(int buf_size);
	virtual ~FileUtils();
public:
	int PrintLines(string fname);
	string GetField(string line,int index, char delimiter);
	string GetField(const char* line,int index, char delimiter);
	int GetFieldsNum(string line,char delimiter);
	void qsortfile(string m_fname1,char delimiter);
	bool OpenFileForRead(string fname);
	bool OpenFileForWrite(string fname);
	void SeekToBegin();
	bool FirstRead();
	bool LastWrite();
	bool AllocBuffer();
	void Release();
	int CountLines(string fname);
	int GetLine(char* line, int line_size);
	int WriteLine(char* line);
	bool WriteLine(string fname,char* line);
	bool WriteLine(string fname,char* line,const char* tag);
private:
	FILE* fp;
	char* g_buffer;
	int g_buf_size;
	int real_buf_size;
	long g_fp_pos;
	int g_buf_pos;
	int out_buf_pos;
	char* out_buffer;
	FILE* out_fp;
private:
	int line_pos[32];
};
#endif
