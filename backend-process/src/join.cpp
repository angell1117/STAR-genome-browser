void JoinFiles(string fname1,string fname2,char delimiter)
{
	char line[1024];

        int ret;
	const int LL = 200000;
	const int SS = 1000000;
	int loci_num;

	int* loci = new int[2*LL];
	int* db = new int[2*MAX*SS];
	if(!loci || !db) return;
	memset(loci, 0, sizeof(int)*2*LL);
	memset(db, 0, sizeof(int)*2*MAX*SS);

	FileUtils file;
	ret = file.OpenFileForRead(fname1);
	if(!ret)
	{
		cout<<"can't open file "<<fname1.c_str()<<" for read!"<<endl;
		exit(-1);
	}

	ret = file.AllocBuffer();
	if(!ret)
	{
		cout<<"can't allocate memory!"<<endl;
		exit(-1);
	}
	ret = file.FirstRead();
	if(!ret) 
	{
		file.Release();
		cout<<"Read file failed!"<<endl;
		exit(-1);
	}

	cout<<"Begin to process:"<<endl;

	int total = 0;
	string fields[10];

	while(1)
	{
		int not_end = file.GetLine(line,1024);
                if(!not_end)
                {
                        Trim(line);
                        if(line[0] == '\0') break;
                }

                int num = 6;
                spliter(line,delimiter,fields,num);

		int value;

		//get chromosome information
		int index = chr2id(fields[0]) - 1;
		if(index < 0 || index >= MAX)
		{
			printf("Warning: chromsome id %s beyond range, skipped!\n", fields[0].c_str());
			continue;
		}
	
		//get start information	
		value = atoi(fields[1].c_str());
		loci[total*2] = index;
		loci[total*2 + 1] = value;
	
		total++; //total count
		if(total%1000 == 0)
		{
			//cout<<"..................."<<total<<" records scanned!"<<endl;
		}
		if(total >= LL)
		{
			cout<<"exceed the boundary limit"<<LL<<endl;
			break;
		}
		if(!not_end) break;
	}
	
	cout<<"total "<<total<<" records were scanned!"<<endl;
	file.Release();	
	loci_num = total;

	FileUtils file1;
	ret = file1.OpenFileForRead(fname2);
	if(!ret)
	{
		cout<<"can't open file "<<fname2.c_str()<<" for read!"<<endl;
		exit(-1);
	}

	ret = file1.AllocBuffer();
	if(!ret)
	{
		cout<<"can't allocate memory!"<<endl;
		exit(-1);
	}
	ret = file1.FirstRead();
	if(!ret) 
	{
		file1.Release();
		cout<<"Read file failed!"<<endl;
		exit(-1);
	}

	cout<<"Begin to process:"<<endl;

	int idx[MAX];
	for(int i = 0; i < MAX; i++) idx[i] = 0;
	total = 0;
	while(1)
	{
		int not_end = file1.GetLine(line,1024);
                if(!not_end)
                {
                        Trim(line);
                        if(line[0] == '\0') break;
                }

                int num = 6;
                spliter(line,delimiter,fields,num);

		//get chromosome information
		int index = chr2id(fields[0]) - 1;
		if(index < 0 || index >= MAX)
		{
			printf("Warning: chromsome id %s beyond range, skipped!\n", fields[0].c_str());
			continue;
		}
		
		double val;
		int tag = -1;
		//get start information	
		double tr = 0.5;
		int pos  = atoi(fields[1].c_str());
		val  = atof(fields[2].c_str());
		if(val >= tr) tag = 0;
		val  = atof(fields[3].c_str());
		if(val >= tr) tag = 1;
		val  = atof(fields[4].c_str());
		if(val >= tr) tag = 2;
		val  = atof(fields[5].c_str());
		if(val >= tr) tag = 3;
		val  = atof(fields[6].c_str());
		if(val >= tr) tag = 4;
		val  = atof(fields[7].c_str());
		if(val >= tr) tag = 5;

		int id = idx[index];
		db[index*2*SS + id*2] = pos;
		db[index*2*SS + id*2 + 1] = tag;
		idx[index]++;
	
		total++; //total count
		if(total%1000 == 0)
		{
			//cout<<"..................."<<total<<" records scanned!"<<endl;
		}
		if(idx[index] >= SS)
		{
			cout<<"exceed the boundary limit"<<SS<<endl;
			break;
		}
		if(!not_end) break;
	}
	
	cout<<"total "<<total<<" records were scanned!"<<endl;
	for(int i = 0; i < MAX; i++)
	{
		 cout<<"chromsome "<<i+1<<":"<<idx[i]<<endl;
	}
	file1.Release();

	int cls[7];
	char annotation[64];
	for(int i = 0; i < 7; i++) cls[i] = 0;

	for(int i = 0; i < loci_num; i++)
	{
		int chr = loci[i*2];
		int pos = loci[i*2 +1];

		int num = idx[chr];
	
		strcpy(annotation,"");	
		for(int j = 0; j < num; j++)
		{
			int pos1 = db[chr*SS*2 + j*2];
			int tag = db[chr*SS*2 + j*2 +1];
			if(pos1 == pos)
			{
				if(tag == 0){ strcpy(annotation, "promoter");cls[0]++;}
				if(tag == 1){ strcpy(annotation, "enhancer");cls[1]++;}
				if(tag == 2){ strcpy(annotation, "background");cls[2]++;}
				if(tag == 3){ strcpy(annotation, "exon");cls[3]++;}
				if(tag == 4){ strcpy(annotation, "repressed");cls[4]++;}
				if(tag == 5){ strcpy(annotation, "intron");cls[5]++;}
				if(tag == -1){ strcpy(annotation, "unknow");cls[6]++;}
				break;
			}
		}
		if(strcmp(annotation,"") == 0)
		{
			strcpy(annotation, "unknow");
			cls[6]++;
		}
		printf("%s\t%d\t%s\n",id2chr(chr+1).c_str(),pos,annotation);
	}
	/*
	printf("Total loci: %d\n",loci_num);
	for(int i = 0; i < 7; i++)
	{
		 printf("%d\n",cls[i]);
	}
	*/
	delete[] loci;
	delete[] db;
}
