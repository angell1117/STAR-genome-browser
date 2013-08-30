############################################################################### 
# 
# Makefile for C/C++ Program 
# 
# Description: 
# The makefile searches in <SRCDIRS> directories for the source files 
# with extensions specified in <SOURCE_EXT>, then compiles the sources 
# and finally produces the <ROGRAM>, the executable file, by linking 
# the objectives. 

# Usage: 
# $ make compile and link the program. 
# $ make objs compile only (no linking. Rarely used). 
# $ make clean clean the objectives and dependencies. 
# $ make cleanall clean the objectives, dependencies and executable. 
# $ make rebuild rebuild the program. The same as make clean && make all. 
#============================================================================== 

## Customizing Section: adjust the following if necessary. 
##============================================================================= 

# The executable file name. 
# It must be specified. 
# PROGRAM := a.out # the executable name 
PROGA := ngsproc
PROGB := fetcher.cgi
PROGC := websrv
PROGD := callMacs

INSTALL_DIR := /usr/local/star
# The directories in which source files reside. 
# At least one path should be specified. 
# SRCDIRS := . # current directory 
SRCDIRS := .

# The source file types (headers excluded). 
# At least one type should be specified. 
# The valid suffixes are among of .c, .C, .cc, .cpp, .CPP, .c++, .cp, or .cxx. 
# SRCEXTS := .c # C program 
# SRCEXTS := .cpp # C++ program 
# SRCEXTS := .c .cpp # C/C++ program 
SRCEXTS := .cpp

# The flags used by the cpp (man cpp for more). 
# CPPFLAGS := -Wall -Werror # show all warnings and take them as errors 
CPPFLAGS := -Wall -Werror

# The compiling flags used only for C. 
# If it is a C++ program, no need to set these flags. 
# If it is a C and C++ merging program, set these flags for the C parts. 
CFLAGS := -W -Wall -std=c99 -pedantic -O2 $(COPT)
CFLAGS += 
LINFLAGS :=  -ldl -pthread 
# The compiling flags used only for C++. 
# If it is a C program, no need to set these flags. 
# If it is a C and C++ merging program, set these flags for the C++ parts. 
CXXFLAGS := -g
CXXFLAGS += -I/usr/local/BerkeleyDB.4.6/include/ -I/usr/local/include/ -I/usr/include/

# The library and the link options ( C and C++ common). 
LDFLAGS := -L/usr/local/BerkeleyDB.4.6/lib/ -L/usr/local/lib/ -ldb_cxx -lpthread -lboost_regex -lz
LDFLAGS += 

## Implict Section: change the following only when necessary. 
##============================================================================= 
# The C program compiler. Uncomment it to specify yours explicitly. 
#CC = gcc 

# The C++ program compiler. Uncomment it to specify yours explicitly. 
CXX = g++ 

# Uncomment the 2 lines to compile C programs as C++ ones. 
#CC = $(CXX) 
#CFLAGS = $(CXXFLAGS) 

# The command used to delete file. 
#RM = rm -f 

## Stable Section: usually no need to be changed. But you can add more. 
##============================================================================= 
#SOURCES = $(foreach d,$(SRCDIRS),$(wildcard $(addprefix $(d)/*,$(SRCEXTS)))) 
#OBJS = $(foreach x,$(SRCEXTS),$(patsubst %$(x),%.o,$(filter %$(x),$(SOURCES)))) 
SHELL = /bin/sh 
SOURCES = ngsproc.cpp FileUtils.cpp ConfUtils.cpp utils.cpp NGSDatabase.cpp
OBJS = ngsproc.o FileUtils.o ConfUtils.o utils.o NGSDatabase.o
SOURCESB = fetcher.cpp FileUtils.cpp ConfUtils.cpp utils.cpp
OBJSB = fetcher.o FileUtils.o ConfUtils.o utils.o
SOURCESC = mongoose.c websrv.c 
OBJSC = mongoose.o websrv.o 
SOURCESD = callMacs.cpp FileUtils.cpp ConfUtils.cpp utils.cpp NGSDatabase.cpp
OBJSD = callMacs.o FileUtils.o ConfUtils.o utils.o NGSDatabase.o
#DEPS = $(patsubst %.o,%.d,$(OBJS)) 

.PHONY : all objs clean cleanall rebuild 

all : $(PROGA) $(PROGB) $(PROGC) $(PROGD)

# Rules for creating the dependency files (.d). 
#--------------------------------------------------- 
%.d : %.c
	@$(CC) -MM -MD $(CFLAGS) $<

%.d : %.C 
	@$(CC) -MM -MD $(CXXFLAGS) $< 

%.d : %.cc 
	@$(CC) -MM -MD $(CXXFLAGS) $< 

%.d : %.cpp 
	@$(CC) -MM -MD $(CXXFLAGS) $< 

%.d : %.CPP 
	@$(CC) -MM -MD $(CXXFLAGS) $< 

%.d : %.c++ 
	@$(CC) -MM -MD $(CXXFLAGS) $< 

%.d : %.cp 
	@$(CC) -MM -MD $(CXXFLAGS) $< 

%.d : %.cxx 
	@$(CC) -MM -MD $(CXXFLAGS) $< 

# Rules for producing the objects. 
#--------------------------------------------------- 
objs : $(OBJS) 

%.o : %.c 
	$(CC) -c $(CPPFLAGS) $(CFLAGS) $< 

%.o : %.C 
	$(CXX) -c $(CPPFLAGS) $(CXXFLAGS) $< 

%.o : %.cc 
	$(CXX) -c $(CPPFLAGS) $(CXXFLAGS) $< 

%.o : %.cpp 
	$(CXX) -c $(CPPFLAGS) $(CXXFLAGS) $< 

%.o : %.CPP 
	$(CXX) -c $(CPPFLAGS) $(CXXFLAGS) $< 

%.o : %.c++ 
	$(CXX -c $(CPPFLAGS) $(CXXFLAGS) $< 

%.o : %.cp 
	$(CXX) -c $(CPPFLAGS) $(CXXFLAGS) $< 

%.o : %.cxx 
	$(CXX) -c $(CPPFLAGS) $(CXXFLAGS) $< 

# Rules for producing the executable. 
#---------------------------------------------- 
$(PROGA) : $(OBJS) 
ifeq ($(strip $(SRCEXTS)), .c) # C file 
$(CC) -o $(PROGA) $(OBJS) $(LDFLAGS) 
else # C++ file 
	$(CXX) -o $(PROGA) $(OBJS) $(LDFLAGS) 
endif 
$(PROGB) : $(OBJSB) 
ifeq ($(strip $(SRCEXTS)), .c) # C file 
$(CC) -o $(PROGB) $(OBJSB) $(LDFLAGS) 
else # C++ file 
	$(CXX) -o $(PROGB) $(OBJSB) $(LDFLAGS) 
endif 
$(PROGC) : $(OBJSC) 
ifeq ($(strip $(SRCEXTS)), .c) # C file 
$(CC) -o $(PROGC) $(OBJSC) $(LDFLAGS) $(LINFLAGS)
else # C++ file 
	$(CXX) -o $(PROGC) $(OBJSC) $(LDFLAGS) $(LINFLAGS)
endif 
$(PROGD) : $(OBJSD) 
ifeq ($(strip $(SRCEXTS)), .c) # C file 
$(CC) -o $(PROGD) $(OBJSD) $(LDFLAGS)
else # C++ file 
	$(CXX) -o $(PROGD) $(OBJSD) $(LDFLAGS)
endif 

linux:
	$(CC) mongoose.c websrv.c -o $(PROGC) $(LINFLAGS) $(CFLAGS)
bsd:
	$(CC) mongoose.c websrv.c -pthread -o $(PROGC) $(CFLAGS)
mac:
	$(CC) mongoose.c websrv.c -pthread -o $(PROGC) $(CFLAGS)
solaris:
	gcc mongoose.c websrv.c -pthread -lnsl -lsocket -o $(PROGC) $(CFLAGS)


#include $(DEPS) 

rebuild: clean all 

install: 
	-mkdir $(INSTALL_DIR)
	-mkdir $(INSTALL_DIR)/$(PROGA)
	-mkdir $(INSTALL_DIR)/$(PROGA)/log
	-cp ../proc_files.pl ../Utils.pm $(INSTALL_DIR)/$(PROGA)/
	-cp $(PROGA) $(INSTALL_DIR)/$(PROGA)/ 
	-cp $(PROGD) $(INSTALL_DIR)/$(PROGA)/ 
	-mkdir $(INSTALL_DIR)/$(PROGC)
	-mkdir $(INSTALL_DIR)/$(PROGC)/cgi-bin
	-mkdir $(INSTALL_DIR)/$(PROGC)/log
	-cp $(PROGC) $(INSTALL_DIR)/$(PROGC)
	-cp $(PROGB) $(INSTALL_DIR)/$(PROGC)/cgi-bin/

clean : 
	@$(RM) *.o *.d $(PROGA) $(PROGB) $(PROGC) $(PROGD)

cleanall: clean 
	@$(RM) $(PROGA) $(PROGB) $(PROGC) $(PROGD)
