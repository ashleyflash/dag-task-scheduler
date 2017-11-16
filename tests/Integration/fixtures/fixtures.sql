USE dag_scheduler_int;

INSERT INTO `process_dependency` (`idprocess_dependency`,`idft_x_server_event_type`,`lock_name`,`log_file_type`,`process_type`,`process_enabled`,`dependency_period`,`process_order`,`split_type`,`job_name`,`job_type`,`queue`,`duplicate_for_timezones`,`force_push`,`priority`,`queue_offset`,`suspended`,`can_rerun`,`suspended_in_turbo`,`start_after`,`idprocess_dependency_group`,`sequential`,`sla`)
VALUES
(2,0,'Uniques','i','hour','y',1,50,'campaign','Job\\Logs\\UniquesUpi','q','',0,0,0,0,'n','y','y','2015-04-01 00:00:00',6,'n',120)
,(4,0,'Processed','i','hour','y',1,1,'logs','Job\\Logs\\Load','q','low',0,1,97,0,'n','y','n','2015-04-01 00:00:00',1,'n',120)
,(8,0,'Processed','c','hour','y',1,1,'logs','Job\\Logs\\Load','q','low',0,1,98,0,'n','y','n','2015-04-01 00:00:00',1,'n',120)
,(11,0,'Spot Summary','s','hour','y',1,20,'beid','Job\\Summary\\Hourly\\SpotMatchesHourly','q','spotsummary',0,0,0,0,'n','y','n','2015-04-01 00:00:00',6,'n',120)
,(12,0,'Uniques','c','hour','y',1,50,'campaign','Job\\Logs\\UniquesUpi','q','',0,0,0,0,'n','y','y','2015-04-01 00:00:00',6,'n',120)
,(13,0,'Log Stats','c,i,s,u','hour','y',1,60,'','Job\\Summary\\Hourly\\StatsHourly','q','',0,0,0,0,'n','y','n','2015-04-01 00:00:00',8,'n',120)
,(17,0,'Downloaded','i','hour','y',1,0,'','Job\\Logs\\Download','q','ftp',0,0,100,0,'n','y','n','2015-04-01 00:00:00',1,'n',120)
,(18,0,'Downloaded','c','hour','y',1,0,'','Job\\Logs\\Download','q','ftp',0,0,101,0,'n','y','n','2015-04-01 00:00:00',1,'n',120)
,(19,0,'Downloaded','s','hour','y',1,0,'','Job\\Logs\\Download','q','ftp',0,0,102,0,'n','y','n','2015-04-01 00:00:00',1,'n',120)
,(20,0,'Processed','s','hour','y',1,1,'logs','Job\\Logs\\Load','q','low',0,1,99,0,'n','y','n','2015-04-01 00:00:00',1,'n',120)
,(21,0,'Spotlight Call Log','s','hour','y',1,40,'beid','Job\\Summary\\Hourly\\SpotlightCallLog','q','',0,0,0,0,'n','y','n','2015-04-01 00:00:00',6,'n',120)
,(22,0,'Spot Report','s','hour','y',1,40,'beid','Job\\Summary\\Hourly\\SpotReport','q','spotsummary',0,0,0,0,'n','y','n','2015-04-01 00:00:00',6,'n',120)
,(28,0,'Daily Summary','s','day','y',24,0,'','Job\\Summary\\Daily\\SpotlightDaily','q','controlservice',1,0,0,0,'n','y','n','2015-04-01 00:00:00',6,'n',120)
,(29,0,'Daily Summary','c','day','y',24,0,'','Job\\Summary\\Daily\\DailySummary','q','controlservice',1,0,0,0,'n','y','n','2015-04-01 00:00:00',6,'n',120)
,(30,0,'Daily Summary','i','day','y',24,0,'','Job\\Summary\\Daily\\DailySummary','q','controlservice',1,0,0,0,'n','y','n','2015-04-01 00:00:00',6,'n',120)
,(34,0,'Spotlight Matching','s','hour','y',1,40,'beid','Job\\Logs\\SpotMatch','q','touchpoint',0,0,0,0,'n','y','n','2015-04-01 00:00:00',7,'y',120)
,(43,0,'Costings','c,i','day','y',1,0,'active_campaign_tz','Job\\Summary\\Daily\\CostingsAll','q','controlservice',1,0,0,0,'n','y','n','2015-04-23 13:00:00',7,'n',120)
,(49,0,'Uniques Summary','i','day','y',1,0,'campaign_tz','Job\\Summary\\Daily\\UniquesSummary','q','',1,0,0,0,'n','y','y','2015-04-01 00:00:00',6,'n',120)
,(70,0,'Unique Views','i','day','y',24,0,'campaign_tz','Job\\Summary\\Daily\\UniqueViewSummary','q','controlservice',1,0,0,0,'n','y','y','2015-04-01 00:00:00',5,'n',120)
,(80,0,'Referrer Summary','i','day','y',24,0,'beid_tz','Job\\Summary\\Daily\\DailyNewReferrerSummary','q','controlservice',1,0,0,0,'n','y','y','2015-04-01 00:00:00',6,'n',120)
,(101,0,'Log Summary','c','hour','y',1,0,'campaign','Job\\Summary\\Hourly\\SummaryHourlyShard','q','controlservice',0,0,0,0,'n','y','n','2015-04-01 00:00:00',6,'n',120)
,(102,0,'Log Summary','i','hour','y',1,0,'campaign','Job\\Summary\\Hourly\\SummaryHourlyShard','q','controlservice',0,0,0,0,'n','y','n','2015-04-01 00:00:00',6,'n',120)
,(111,0,'Archiving','c','hour','y',1,4,'beid','Job\\Logs\\Export\\FullLogExtract','q','archiving',0,0,0,0,'n','y','n','2015-04-01 00:00:00',1,'n',120)
,(112,0,'Archiving','i','hour','y',1,4,'beid','Job\\Logs\\Export\\FullLogExtract','q','archiving',0,0,0,0,'n','y','n','2015-04-01 00:00:00',1,'n',120)
,(114,0,'Archiving','s','hour','y',1,4,'beid','Job\\Logs\\Export\\FullLogExtract','q','archiving',0,0,0,0,'n','y','n','2015-04-01 00:00:00',1,'n',120)
,(119,0,'Uniques Summary','c','day','y',1,0,'campaign_tz','Job\\Summary\\Daily\\UniquesSummary','q','',1,0,0,0,'n','y','y','2015-04-01 00:00:00',6,'n',120)
,(124,0,'Touchpoint Matching','j,s','day','y',1,0,'beid_tz','Job\\Logs\\InteractionMatch','q','',1,0,0,24,'n','y','y','2015-04-01 00:00:00',6,'n',120)
,(126,0,'Touchpoint Sequencer','s','day','y',1,0,'beid_tz','Job\\Summary\\Daily\\TouchPointSequencing','q','touchpoint',1,0,0,0,'n','y','y','2015-04-01 00:00:00',7,'n',120)
,(127,0,'User Activity Build','c','hour','y',1,0,'beid','Job\\Summary\\Hourly\\BuildGuidnIpnCountTable','q','',0,0,0,0,'n','y','y','2015-04-01 00:00:00',5,'n',120)
,(128,0,'User Activity Build','i','hour','y',1,0,'beid','Job\\Summary\\Hourly\\BuildGuidnIpnCountTable','q','',0,0,0,0,'n','y','y','2015-04-01 00:00:00',5,'n',120)
,(129,0,'User Activity Build','s','hour','y',1,0,'beid','Job\\Summary\\Hourly\\BuildGuidnIpnCountTable','q','',0,0,0,0,'n','y','y','2015-04-01 00:00:00',5,'n',120)
,(130,0,'Invalid Clicks','c','hour','y',1,0,'beid','Job\\Summary\\Hourly\\BuildInvalidClickTable','q','',0,0,0,0,'n','y','y','2015-04-01 00:00:00',6,'n',120)
,(132,0,'Log Summary','s','hour','y',1,20,'spotgroup','Job\\Summary\\Hourly\\SpotSummaryHourlyShard','q','spotsummary',0,0,0,0,'n','y','n','2015-04-01 00:00:00',6,'n',120)
,(133,0,'Log Summary','s','day','y',24,20,'spotgroup_tz','Job\\Summary\\Daily\\SpotLogDaily','q','',1,0,0,0,'n','y','n','2015-04-01 00:00:00',6,'n',120)
,(134,0,'Suspect GUIDN','c,i,s','day','y',24,20,'','Job\\Summary\\Daily\\SuspectUsersSummary','q','controlservice',0,0,0,0,'n','y','y','2015-04-01 00:00:00',5,'n',120)
,(137,0,'Suspect IPN','c,i,s','day','y',24,20,'','Job\\Summary\\Daily\\SuspectIpnSummary','q','controlservice',0,0,0,0,'n','y','y','2015-04-01 00:00:00',5,'n',120)
,(144,0,'Purged Summary','i','day','y',24,20,'beid','Job\\Summary\\Daily\\PurgedSummary','q','',0,0,0,0,'n','y','n','2015-04-01 00:00:00',6,'n',120)
,(145,0,'Purged Summary','c','day','y',24,20,'beid','Job\\Summary\\Daily\\PurgedSummary','q','',0,0,0,0,'n','y','n','2015-04-01 00:00:00',6,'n',120)
,(146,0,'Purged Summary','s','day','y',24,20,'beid','Job\\Summary\\Daily\\PurgedSummary','q','',0,0,0,0,'n','y','n','2015-04-01 00:00:00',6,'n',120)
,(155,0,'Internal Traffic','c,i','hour','y',1,0,'beid','Job\\Summary\\Hourly\\InternalTrafficHourly','q','',0,0,0,0,'n','y','y','2015-04-01 00:00:00',5,'n',120)
,(160,0,'Processing Complete','c','hour','y',1,2,'','Job\\Control\\ProcessingComplete','q','',0,0,0,0,'n','y','n','2015-04-01 00:00:00',1,'n',120)
,(161,0,'Processing Complete','i','hour','y',1,2,'','Job\\Control\\ProcessingComplete','q','',0,0,0,0,'n','y','n','2015-04-01 00:00:00',1,'n',120)
,(162,0,'Processing Complete','s','hour','y',1,2,'','Job\\Control\\ProcessingComplete','q','',0,0,0,0,'n','y','n','2015-04-01 00:00:00',1,'n',120)
,(169,0,'Touchpoint CSV Extract','s','day','y',1,0,'tpt_extract','Job\\Extract\\TouchPointExtract','q','extract',1,0,0,0,'n','y','n','2015-05-19 00:00:00',6,'n',120)
,(170,0,'Geo Campaign Level','c,i','day','y',1,0,'','Job\\Summary\\Daily\\GeoDailyCampaignLevel','q','controlservice',1,0,0,0,'n','y','y','2015-04-01 00:00:00',6,'n',120)
,(173,0,'Export Spots Matched','c,i,s','hour','y',1,0,'beid','Job\\Logs\\Export\\FullLogExtract','q','archiving',0,0,0,0,'n','y','y','2015-04-01 00:00:00',6,'n',120)
,(181,0,'Downloaded','x','hour','y',1,0,'','Job\\Logs\\Download','q','ftp',0,0,100,0,'n','y','y','2015-04-01 00:00:00',1,'n',120)
,(182,0,'Processed','x','hour','y',1,1,'logs','Job\\Logs\\Load','q','low',0,1,0,0,'n','y','y','2015-04-01 00:00:00',1,'n',120)
,(183,0,'Info Summary','x','hour','y',1,20,'','Job\\Summary\\Hourly\\InfoErrorSummary','q','controlservice',0,0,0,0,'n','y','y','2015-04-01 00:00:00',6,'n',120)
,(184,0,'Lifetime Value Transactions','c,i,s','hour','y',1,60,'beid_lifetime_value','Job\\Summary\\Hourly\\SpotLifeTimeValueSummary','q','spotsummary',0,0,0,0,'n','y','y','2015-04-01 00:00:00',7,'n',120)
,(185,0,'Downloaded','l','day','y',1,0,'','Job\\Logs\\Download','q','ftp',0,0,100,0,'n','y','y','2015-04-01 00:00:00',6,'n',120)
,(186,0,'Processed','l','day','y',24,1,'logs','Job\\Logs\\Load','q','low',0,1,0,0,'n','y','y','2015-04-01 00:00:00',6,'n',120)
,(187,0,'Beid Log Stats','c,i,s','hour','y',1,1,'beid','Job\\Logs\\BeidLogStats','q','',0,0,0,0,'n','y','n','2015-04-01 00:00:00',6,'n',120)
,(189,0,'Processed','u','hour','y',1,1,'logs','Job\\Logs\\Load','q','controlservice',0,1,0,0,'n','y','y','2015-04-01 00:00:00',1,'n',120)
,(190,0,'Downloaded','u','hour','y',1,0,'','Job\\Logs\\Download','q','ftp',0,0,100,0,'n','y','y','2015-04-01 00:00:00',1,'n',120)
,(191,0,'Processing Complete','u','hour','y',1,2,'','Job\\Control\\ProcessingComplete','q','',0,0,0,0,'n','y','y','2015-04-01 00:00:00',1,'n',120)
,(192,0,'Segment Opt Summary','u','day','y',24,20,'','Job\\Summary\\Daily\\SegmentOptSummary','q','controlservice',0,0,0,0,'n','y','y','2015-04-01 00:00:00',6,'n',120)
,(193,0,'Downloaded','j','hour','y',1,0,'','Job\\Logs\\Download','q','ftp',0,0,100,0,'n','y','y','2015-04-01 00:00:00',1,'n',120)
,(194,0,'Processed','j','hour','y',1,1,'logs','Job\\Logs\\Load','q','interactions',0,1,0,0,'n','y','y','2015-04-01 00:00:00',1,'n',120)
,(197,0,'Daily Summary','j','day','y',1,0,'','Job\\Summary\\Daily\\DailySummary','q','controlservice',1,0,0,4,'n','y','y','2015-04-01 00:00:00',6,'n',120)
,(198,0,'Processing Complete','x','hour','y',1,2,'','Job\\Control\\ProcessingComplete','q','',0,0,0,0,'n','y','y','2015-04-01 00:00:00',1,'n',120)
,(205,0,'Processed','e','hour','y',1,1,'logs','Job\\Logs\\Load','q','low',0,1,97,0,'n','y','n','2015-04-01 00:00:00',1,'n',120)
,(206,0,'Downloaded','e','hour','y',1,0,'','Job\\Logs\\Download','q','ftp',0,0,100,0,'n','y','n','2015-04-01 00:00:00',1,'n',120)
,(207,0,'Processing Complete','e','hour','y',1,2,'','Job\\Control\\ProcessingComplete','q','',0,0,0,0,'n','y','n','2015-04-01 00:00:00',1,'n',120)
,(208,0,'Summary Check','c,i,s','day','y',1,0,'','Job\\Summary\\DataChecking\\SummaryCheck','q','',1,0,0,7,'n','y','n','2015-04-01 00:00:00',7,'n',120)
,(210,0,'Update Impression State','e','hour','y',1,2,'beid','Job\\Logs\\UpdateImpressionsFromState','q','',0,0,0,0,'n','y','n','2015-04-01 00:00:00',7,'y',120)
,(242,25,'Summary Extract - Transaction','s','day','y',1,2,'','Job\\Extract\\Summary\\SummaryExtract','q','high',1,0,0,0,'n','y','n','2015-04-01 00:00:00',13,'n',120)
,(243,10,'Summary Extract - Daily','j','day','y',1,2,'','Job\\Extract\\Summary\\SummaryExtract','q','high',1,0,0,0,'n','y','n','2015-04-01 00:00:00',13,'n',120)
,(244,8,'Summary Extract - Daily','s','day','y',1,2,'','Job\\Extract\\Summary\\SummaryExtract','q','high',1,0,0,0,'n','y','n','2015-04-01 00:00:00',13,'n',120)
,(245,7,'Summary Extract - Daily','c','day','y',1,2,'','Job\\Extract\\Summary\\SummaryExtract','q','high',1,0,0,0,'n','y','n','2015-04-01 00:00:00',13,'n',120)
,(246,3,'Summary Extract - Daily','i','day','y',1,2,'','Job\\Extract\\Summary\\SummaryExtract','q','high',1,0,0,0,'n','y','n','2015-04-01 00:00:00',13,'n',120)
,(249,0,'Update Click State','e','hour','y',1,3,'beid','Job\\Logs\\UpdateClicksFromState','q','',0,0,0,0,'n','y','n','2015-04-01 00:00:00',7,'n',120)
,(250,0,'User Match','c','hour','y',1,4,'adv_attr_beid','Job\\Logs\\CreateDeviceIdMap','q','',0,0,0,0,'n','y','n','2015-04-01 00:00:00',7,'n',120)
,(251,0,'User Match','i','hour','y',1,4,'adv_attr_beid','Job\\Logs\\CreateDeviceIdMap','q','',0,0,0,0,'n','y','n','2015-04-01 00:00:00',7,'n',120)
,(252,0,'User Match','s','hour','y',1,4,'adv_attr_beid','Job\\Logs\\CreateDeviceIdMap','q','',0,0,0,0,'n','y','n','2015-04-01 00:00:00',7,'n',120)
,(264,0,'Business Extract - Meta Data','i,c,s','day','y',1,0,'','Job\\Scheduled\\ExportBusinessData','q','high',0,0,0,2,'n','y','n','2015-04-01 00:00:00',13,'n',120)
,(265,26,'Summary Extract - Trans V2','s','day','y',1,2,'','Job\\Extract\\Summary\\SummaryExtract','q','high',1,0,0,0,'n','y','n','2015-04-01 00:00:00',13,'n',120)
,(267,0,'Impression Daily Aggregate','c,i','day','y',24,0,'beid_tz','Job\\Summary\\LogAggregate','q','',1,0,0,0,'n','y','y','2015-04-01 00:00:00',5,'y',120)
,(268,0,'Interaction Daily Aggregate','j','day','y',1,0,'beid_tz','Job\\Summary\\LogAggregate','q','',1,0,0,0,'n','y','y','2015-04-01 00:00:00',5,'y',120)
,(269,0,'Attribution Daily Aggregate','s','day','y',24,0,'beid_tz','Job\\Summary\\LogAggregate','q','',1,0,0,0,'n','y','y','2015-04-01 00:00:00',5,'y',120)
,(270,0,'Extract - Attribution Daily','s','day','y',24,0,'','Job\\Extract\\Aggregate\\AggregateExtract','q','',1,0,0,0,'n','y','y','2015-04-01 00:00:00',5,'n',120)
,(271,0,'Extract - Interaction Daily','j','day','y',1,0,'','Job\\Extract\\Aggregate\\AggregateExtract','q','',1,0,0,0,'n','y','y','2015-04-01 00:00:00',5,'n',120)
,(272,0,'Extract - Impression Daily','c,i','day','y',24,0,'','Job\\Extract\\Aggregate\\AggregateExtract','q','',1,0,0,0,'n','y','y','2015-04-01 00:00:00',5,'n',120)
,(275,0,'Create Client Match Export','c,i,s','day','y',1,0,'beid','Job\\Logs\\Export\\ClientMatchExtract','q','archiving',0,0,0,0,'n','y','n','2015-04-01 00:00:00',9,'n',120)
,(276,0,'Create Shared Match Export','c,i,s','day','y',1,0,'','Job\\Logs\\Export\\SharedMatchExtract','q','archiving',0,0,0,0,'n','y','n','2015-04-01 00:00:00',9,'n',120);



INSERT INTO `process_dependency_tree` (`idprocess_dependency`,`idprocess_dependent`)
VALUES -- (30,102),(102,107),(107,161),(89,30),(152,30),(2000,30),(2001,30),(2002,30);
(2,161),(11,34),(12,160),(13,34),(13,160),(13,161),(13,162),(21,162),(22,34),(28,11),(29,101),(30,102),(34,160),(34,161),(34,162),(34,250),(34,251),(34,252),(49,2),(49,30),(70,2),(80,161),(101,160),(102,161),(102,210),(111,160),(112,161),(114,162),(119,12),(119,29),(124,28),(126,28),(126,124),(127,160),(128,161),(129,162),(130,160),(130,161),(132,162),(133,132),(134,127),(134,128),(134,129),(137,127),(137,128),(137,129),(144,161),(145,160),(146,162),(155,160),(155,161),(160,18),(161,17),(162,19),(170,267),(173,34),(182,181),(183,182),(183,198),(184,34),(186,184),(187,160),(187,161),(187,162),(191,190),(192,191),(193,161),(198,181),(207,206),(210,161),(210,207),(210,249),(242,22),(243,197),(244,28),(245,29),(246,30),(249,160),(249,207),(250,249),(251,210),(252,162),(265,22),(267,160),(267,161),(267,210),(267,249),(268,197),(269,34),(270,269),(271,268),(272,267);

INSERT INTO `process_dirty_value` (`value_type`,`idvalue`,`log_filename_date`,`log_file_type`,`log_filename_date_only`)
VALUES
('b',100,'2015-05-04 03:00:00','j','2015-05-04'),
('b',1638,'2015-05-04 03:00:00','j','2015-05-04'),
('b',3936,'2015-05-04 03:00:00','j','2015-05-04'),
('b',5075,'2015-05-04 03:00:00','j','2015-05-04'),
('b',100,'2015-05-20 03:00:00','j','2015-05-20'),
('b',1638,'2015-05-20 03:00:00','j','2015-05-20'),
('b',3936,'2015-05-20 03:00:00','j','2015-05-20'),
('b',100,'2015-05-21 09:00:00','c','2015-05-21'),
('b',100,'2015-05-21 09:00:00','i','2015-05-21'),
('b',1638,'2015-05-21 09:00:00','c','2015-05-21'),
('b',1638,'2015-05-21 09:00:00','i','2015-05-21'),
('b',1638,'2015-05-21 09:00:00','s','2015-05-21'),
('b',3936,'2015-05-21 09:00:00','c','2015-05-21'),
('b',3936,'2015-05-21 09:00:00','i','2015-05-21'),
('b',4657,'2015-05-21 09:00:00','c','2015-05-21'),
('b',4657,'2015-05-21 09:00:00','i','2015-05-21'),
('b',4657,'2015-05-21 09:00:00','s','2015-05-21'),
('b',5028,'2015-05-21 09:00:00','c','2015-05-21'),
('b',5028,'2015-05-21 09:00:00','e','2015-05-21'),
('b',5028,'2015-05-21 09:00:00','i','2015-05-21'),
('b',5028,'2015-05-21 09:00:00','s','2015-05-21'),
('b',5075,'2015-05-21 09:00:00','c','2015-05-21'),
('b',5075,'2015-05-21 09:00:00','i','2015-05-21'),
('b',5075,'2015-05-21 09:00:00','s','2015-05-21'),
('c',101,'2015-05-21 09:00:00','c','2015-05-21'),
('c',10001,'2015-05-21 09:00:00','i','2015-05-21'),
('c',10003,'2015-05-21 09:00:00','c','2015-05-21'),
('c',10101,'2015-05-21 09:00:00','i','2015-05-21'),
('c',31003,'2015-05-21 09:00:00','c','2015-05-21'),
('c',31003,'2015-05-21 09:00:00','i','2015-05-21'),
('c',34195,'2015-05-21 09:00:00','c','2015-05-21'),
('c',34195,'2015-05-21 09:00:00','i','2015-05-21'),
('c',43093,'2015-05-21 09:00:00','c','2015-05-21'),
('c',43093,'2015-05-21 09:00:00','i','2015-05-21'),
('c',43503,'2015-05-21 09:00:00','c','2015-05-21'),
('c',43503,'2015-05-21 09:00:00','i','2015-05-21'),
('c',45862,'2015-05-21 09:00:00','c','2015-05-21'),
('c',45862,'2015-05-21 09:00:00','i','2015-05-21'),
('s',1477,'2015-05-21 09:00:00','s','2015-05-21'),
('s',3196,'2015-05-21 09:00:00','s','2015-05-21'),
('s',3528,'2015-05-21 09:00:00','s','2015-05-21'),
('s',3529,'2015-05-21 09:00:00','s','2015-05-21'),
('s',3572,'2015-05-21 09:00:00','s','2015-05-21'),
('s',3615,'2015-05-21 09:00:00','s','2015-05-21');

INSERT INTO `process_lock_expected` (`idprocess_dependency`,`expected_lock_value`,`log_filename_date`)
VALUES
(107,6,'2015-05-21 09:00:00'),
(102,7,'2015-05-21 09:00:00'),
(107,1,'2015-05-21 00:00:00'),
(102,1,'2015-05-21 00:00:00'),
(30,2,'2015-05-21 00:00:00'),
(89,3,'2015-05-21 00:00:00'),
(152,4,'2015-05-21 00:00:00'),
(2000,2,'2015-05-21 00:00:00'),
(2001,2,'2015-05-21 00:00:00'),
(2002,2,'2015-05-21 00:00:00');

INSERT INTO `process_lock` (`idprocess_dependency`,`lock_value`,`log_filename_date`,`process_status`)
VALUES
(107,100,'2015-05-21 09:00:00',95),
(107,1638,'2015-05-21 09:00:00',95),
(107,3936,'2015-05-21 09:00:00',95),
(107,4657,'2015-05-21 09:00:00',95),
(107,5028,'2015-05-21 09:00:00',50),
(107,5075,'2015-05-21 09:00:00',95),
(102,10001,'2015-05-21 09:00:00',95),
(102,10101,'2015-05-21 09:00:00',95),
(102,31003,'2015-05-21 09:00:00',20),
(102,34195,'2015-05-21 09:00:00',95),
(102,43093,'2015-05-21 09:00:00',95),
(102,43503,'2015-05-21 09:00:00',95),
(102,45862,'2015-05-21 09:00:00',95),
(107,100,'2015-05-21 00:00:00',95),
(102,100,'2015-05-21 00:00:00',95),
(30,1,'2015-05-21 00:00:00',50),
(89,1,'2015-05-21 00:00:00',60),
(152,1,'2015-05-21 00:00:00',20),
(2000,1,'2015-05-21 00:00:00',20),
(2001,1,'2015-05-21 00:00:00',20),
(2002,1,'2015-05-21 00:00:00',20);