#ifndef __CONFUTILS_H
#define __CONFUTILS_H

#include <string>
using namespace std;
#pragma once

class ConfUtils  
{
public:
	ConfUtils();
	virtual ~ConfUtils();
public:
 	int GetPara(char* line,const char* para,char* value);
	int GetConfString(string fname,const char*  para,char* value);
private:
};
#endif
