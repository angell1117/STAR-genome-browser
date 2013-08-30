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

using namespace std;
using namespace boost;

const char* basedir = "/data/annoj-analysis-data/";
const char* dbdir = "/data/annoj-analysis/";
const char* log_dir = "/usr/local/star/ngsproc/log";

int main(int argc, char* argv[]) 
{
    char current[256];
    int ret;
    char x_name[256];
    char o_name[256];
    char output_name[256];

    if(argc < 3)
    {
        cout<<"Usage: callMacs dbname ouput"<<endl;
        return 0;
    }

    strncpy(x_name, argv[1], 255);
    strncpy(o_name, argv[2], 255);
    strncpy(output_name, o_name, 255);
 
    NGSDatabase ngs(64);

    time_t t = time(NULL);
    char cmd[256];
    sprintf(cmd, ".%ld", t);
    strcat(o_name, cmd);

    int total = ngs.Retrieve(x_name, o_name, basedir);
    if(total < 0)
    {
        WriteLog(log_dir,"Failed to retrieve data from database!");
        return 0;
    }
    getcwd(current, 256);
    string dir = basedir;
    dir += o_name;
    ret = chdir(dir.c_str());
    if(ret < 0) return 0;
    
    sprintf(cmd,"macs -t %s -f BED -p 1e-4", o_name);
    WriteLog(log_dir,cmd);
    ret = system(cmd);
    if(ret < 0) {
        WriteLog(log_dir,"macs Failed!");
        return 0;
    }
    WriteLog(log_dir,"macs successfully finished!");

    string peak = dir + "/NA_peaks.bed";
    if(access(peak.c_str(), F_OK) != 0)
    {
        sprintf(cmd,"%s does not exist!\n", peak.c_str());
        WriteLog(log_dir,cmd);
        return 0;
    }

    string format = "1:2:3:-1:-1:-1";

    ngs.Deposit(peak, output_name, format, dbdir);
    
    sprintf(cmd, "rm -rf %s", dir.c_str());
    system(cmd);
    chdir(current);
    return 0;
} 
