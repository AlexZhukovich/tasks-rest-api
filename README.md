# RESTful API for the Task list
A basic RESTful API with token-based authentication in PHP for "Task List" application.

*Note: It's a test project. Don't use in for production application.*

## Technologies and tools:
* PHP
* MySQL

## Set up
I used the *Apache* server. However, it should work on any server which supports `.htacccess` files because it contains routing information.

## MySQL database
**Database name:** *tasks-db*.

**Tables:**
* Users (tbl_users)
* Sessions (tbl_sessions)
* Tasks (tbl_tasks)

*Note: If you want to use different names of database and tables, remember to update source code.*

### Structure of Users table (tbl_users).
**Charset:** utf8_general_ci
**Structure:**
| Column        | Type            | Collation    | Default value | Auto increment | Comments       |
| ------------- | --------------- | ------------ | :-----------: | :------------: | -------------- |
| id            | BIGINT          |              |               | **X**          | User ID        |
| fullname      | VARCHAR (256)   |              |               |                | User Full Name |	
| username      | VARCHAR (256)   |              |               |                | User username  | 
| password      | VARCHAR (256)   | **utf8_bin** |               |                | User password  | 	
| useractive    | ENUM ('N', 'Y') |              | **'Y'**       |                | User password  | 	
| loginattempts | INT (1)         |              | **0**         |                | User password  |

**SQL query:**
```
CREATE TABLE `tbl_users` (
  `id` bigint(20) NOT NULL COMMENT 'User ID',
  `fullname` varchar(256) NOT NULL COMMENT 'Users Full Name',
  `username` varchar(255) NOT NULL COMMENT 'Users username',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Users password',
  `useractive` enum('N','Y') NOT NULL DEFAULT 'Y' COMMENT 'Is User active',
  `loginattempts` int(1) NOT NULL DEFAULT '0' COMMENT 'Attempts to log in'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `tbl_users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'User ID', AUTO_INCREMENT=6;
COMMIT;
```

**Notes:**
* **id** - PRIMARY KEY
* **username** - UNIQUE KEY

### Structure of Sessions table (tbl_sessions).
**Charset:** utf8_general_ci
**Structure:**
| Column             | Type          | Collation    | Auto increment | Comments                       |
| ------------------ | ------------- | ------------ | :------------: | ------------------------------ |
| id                 | BIGINT        |              | **X**          | Session ID                     |
| userid             | BIGINT        |              |                | User ID                        |
| accesstoken        | VARCHAR (256) | **utf8_bin** |                | Access Token                   |
| accesstokenexpiry  | DATETIME      |              |                | Access Token Expiry Date/Time  |
| refreshtoken       | VARCHAR (256) | **utf8_bin** |                | RefreshToken                   |
| refreshtokenexpiry | DATETIME      |              |                | Refresh Token Expiry Date/Time |

**SQL query:**
```
CREATE TABLE `tbl_sessions` (
  `id` bigint(20) NOT NULL COMMENT 'Session ID',
  `userid` bigint(20) NOT NULL COMMENT 'User ID',
  `accesstoken` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Access Token',
  `accesstokenexpiry` datetime NOT NULL COMMENT 'Access Token Expiry Date/Time',
  `refreshtoken` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'RefreshToken',
  `refreshtokenexpiry` datetime NOT NULL COMMENT 'Refresh Token Expiry Date/Time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Sessions table';

ALTER TABLE `tbl_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `acceesstoken` (`accesstoken`),
  ADD UNIQUE KEY `refreshtoken` (`refreshtoken`),
  ADD KEY `sessionuserid_fk` (`userid`);

ALTER TABLE `tbl_sessions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Session ID', AUTO_INCREMENT=3;  

ALTER TABLE `tbl_sessions`
  ADD CONSTRAINT `sessionuserid_fk` FOREIGN KEY (`userid`) REFERENCES `tbl_users` (`id`);
COMMIT;
```

**Notes:** 
* **id** - PRIMARY KEY
* **accesstoken** - UNIQUE KEY
* **refreshtoken** - UNIQUE KEY
* **userid** - FOREIGN KEY of `tbl_users.id`

### Structure of Tasks table (tbl_tasks).
**Charset:** utf8_general_ci
**Structure:**
| Column      | Type           | Default value | Auto increment | Comments                |
| ----------- | -------------- | :-----------: | :------------: | ----------------------- |
| id          | BIGINT         |               | **X**          | Task ID                 |
| title       | VARCHAR (256)  |               |                | Task Title              | 
| description | MEDIUMTEXT     |               |                | Task Description        | 	
| deadline    | DATETIME       |               |                | Task Deadline Date      |
| completed   | ENUM('Y', 'N') | **'N'**       |                | Task Completion Status  |
| userid      | BIGINT         |               |                | User ID - owner of task |

**SQL query:**
```
CREATE TABLE `tbl_tasks` (
  `id` bigint(20) NOT NULL COMMENT 'Task ID - Primary Key',
  `title` varchar(255) NOT NULL COMMENT 'Task Title',
  `description` mediumtext COMMENT 'Task Description',
  `deadline` datetime DEFAULT NULL COMMENT 'Task Deadline Date',
  `completed` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT 'Task Completion Status',
  `userid` bigint(20) NOT NULL COMMENT 'User ID of owner of task'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tasks table';

ALTER TABLE `tbl_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `taskuserid_fk` (`userid`);

ALTER TABLE `tbl_tasks`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Task ID - Primary Key', AUTO_INCREMENT=4;

ALTER TABLE `tbl_tasks`
  ADD CONSTRAINT `taskuserid_fk` FOREIGN KEY (`userid`) REFERENCES `tbl_users` (`id`);
COMMIT;  
```

**Notes:**
* **id** - PRIMARY KEY
* **userid** - FOREIGN KEY of `tbl_users.id`

## Routes
* **/v1/users/**
  * **[POST]** Create a new user account.
* **/v1/sessions/** 
  * **[POST]** Create a new user session.
* **/v1/sessions/SESSION_ID**
  * **[PATCH]** Refresh access and refresh tokens, based on `SESSION_ID`.
  * **[DELETE]** Delete existing session.
* **/v1/tasks/**
  * **[GET]** Get tasks based on logged in user. 
  * **[POST]** Create a new task for the logged in user.
* **/v1/tasks/page/PAGE**
  * **[GET]** Get tasks based on logged in user for the `PAGE` (each page contains 20 tasks. It can be modified in the source code).
* **/v1/tasks/complete**
  * **[GET]** Get list of completed tasks for logged in user. 
* **/v1/tasks/incomplete**
  * **[GET]** Get list of incompleted tasks for logged in user.
* **/v1/tasks/TASK_ID**
  * **[GET]** Get tasks by `TASK_ID`, based on logged in user.
  * **[PATCH]** Update task by `TASK_ID`, based on logged in user.
  * **[DELETE]** Delete task by `TASK_ID`, based on logged in user.