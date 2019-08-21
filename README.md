# RESTful API for the Task list
A basic RESTful API with token-based authentication in PHP for "Task List" application.

*Note: It's a test project. Don't use in for production application.*

## Technologies and tools:
* PHP
* MySQL

## Set up
I used the *Apache* server. However, it should work on any server which supports `.htacccess` files because it contains routing information.

**Note:** *The `php_flag display_errors` on should be removed from the `.htaccess` file before installing to server.*

## MySQL database
**Database name:** *tasks-db*.

**Tables:**
* **Users** (tbl_users)
* **Sessions** (tbl_sessions)
* **Tasks** (tbl_tasks)

*Note: If you want to use different names of database and tables, remember to update source code.*

## Use Cases
Basic use case of using API for creating account, adding tasks to logged in user and log out.

1. [Create a new account](#create_user_account)
2. [Log In](#create_user_session)
3. [Add a new task](#add-task)
4. [Get all tasks](#get-tasks) or [Get all task split by pages](#get-tasks-by-page)
5. [Log Out](#delete_existing_session)

Additional features:
1. [Update an existing task](#update-task-by-id)
2. [Delete an existing task](#delete-task-by-id)
3. [Get completed tasks](#get-completed-tasks)
4. [Get incompleted tasks](#get-incompleted-tasks)


## Routes
* **/v1/users/**
  * **<code>POST</code>** Create a new user account ([see requirements](#create_user_account)).
* **/v1/sessions/** 
  * **<code>POST</code>** Log in (Create a new user session) ([see requirements](#create_user_session)).
* **/v1/sessions/SESSION_ID**
  * **<code>PATCH</code>** Refresh access and refresh tokens, based on `SESSION_ID`. ([see requirements](#refresh-tokens)).
  * **<code>DELETE</code>** Log out (Delete existing session) ([see requirements](#delete_existing_session)).
* **/v1/tasks/**
  * **<code>GET</code>** Get all tasks ([see requirements](#get-tasks)). 
  * **<code>POST</code>** Create a new task ([see requirements](#add-task)).
* **/v1/tasks/page/PAGE**
  * **<code>GET</code>** Get tasks by page number ([see requirements](#get-tasks-by-page)).
* **/v1/tasks/complete**
  * **<code>GET</code>** Get list of completed tasks ([see requirements](#get-completed-tasks)). 
* **/v1/tasks/incomplete**
  * **<code>GET</code>** Get list of incompleted tasks ([see requirements](#get-incompleted-tasks)).
* **/v1/tasks/TASK_ID**
  * **<code>GET</code>** Get tasks by ID ([see requirements](#get-task-by-id)).
  * **<code>PATCH</code>** Update task by ID ([see requirements](#update-task-by-id)).
  * **<code>DELETE</code>** Delete task by ID ([see requirements](#delete-task-by-id)).

## Request requirements 
Description of basic requirements for each request.

### <a name="create_user_account"></a>Create a new user account

| Label  | Value                           |
| ------ |-------------------------------- |
| Method | **<code>POST</code>**           |
| URL    | /tasks-rest-api/v1/users        |
| Header | Content-Type : application/json |
| Body   | {<br>&nbsp;&nbsp;&nbsp;&nbsp;"fullname":`"FULL_NAME"`, <br>&nbsp;&nbsp;&nbsp;&nbsp;"username":`"USER_NAME"`, <br>&nbsp;&nbsp;&nbsp;&nbsp;"password":`"PASSWORD"`<br>}|

The request contains user data: `FULL_NAME`, `USER_NAME` and `PASSWORD`.

**Response**:
```
{
    "statusCode": 201,
    "success": true,
    "messages": ["User created"],
    "data": {
        "user_id": "ID",
        "fullname": "FULL_NAME",
        "username": "USER_NAME"
    }
}
```

### <a name="create_user_session"></a>Log in (Create a new user session)

| Label  | Value                           |
| ------ |-------------------------------- |
| Method | **<code>POST</code>**           |
| URL    | /tasks-rest-api/v1/sessions     |
| Header | Content-Type : application/json |
| Body   | {<br>&nbsp;&nbsp;&nbsp;&nbsp;"username":`"USER_NAME"`, <br>&nbsp;&nbsp;&nbsp;&nbsp;"password":`"PASSWORD"`<br>}|

The request contains user data: `USER_NAME` and `PASSWORD`.

**Response**:
```
{
    "statusCode": 200,
    "success": true,
    "messages": [],
    "data": {
        "user_id": "ID",
        "fullname": "FULL_NAME",
        "username": "USER_NAME"
    }
}
```

### <a name="refresh-tokens"></a>Refresh access and refresh tokens

| Label  | Value                                      |
| ------ |------------------------------------------- |
| Method | **<code>PATCH</code>**                     |
| URL    | /tasks-rest-api/v1/sessions/`SESSION_ID` |
| Header | Content-Type : application/json<br>Authorization : `ACCESS_TOKEN` |
| Body   | {<br>&nbsp;&nbsp;&nbsp;&nbsp;"refresh_token":`"REFRESH_TOKEN"`<br>}|

The request contains user data: `SESSION_ID`, `ACCESS_TOKEN` and `REFRESH_TOKEN`.

**Response:**
```
{
    "statusCode": 200,
    "success": true,
    "messages": ["Token refreshed"],
    "data": {
        "session_id": SESSION_ID,
        "access_token": "ACCESS_TOKEN",
        "access_token_expires_in": ACCESS_TOKEN_EXPIRY,
        "refresh_token": "REFRESH_TOKEN",
        "refresh_token_expires_in": REFRESH_TOKEN_EXPIRY
    }
}
```

### <a name="delete_existing_session"></a>Log out (Delete existing session)

| Label  | Value                                      |
| ------ |------------------------------------------- |
| Method | **<code>DELETE</code>**                    |
| URL    | /tasks-rest-api/v1/sessions/`SESSION_ID`   |
| Header | Authorization : `ACCESS_TOKEN`             |

The request contains user data: `SESSION_ID` and `ACCESS_TOKEN`.

**Response:**
```
{
    "statusCode": 200,
    "success": true,
    "messages": ["Logged out"],
    "data": {
        "session_id": SESSION_ID
    }
}
```

### <a name="get-tasks"></a>Get all tasks

| Label  | Value                                      |
| ------ |------------------------------------------- |
| Method | **<code>GET</code>**                       |
| URL    | /tasks-rest-api/v1/tasks                   |
| Header | Authorization : `ACCESS_TOKEN`             |

The request contains user data: `ACCESS_TOKEN`.

**Response:**
```
{
    "statusCode": 200,
    "success": true,
    "messages": [],
    "data": {
        "row_returned": TASKS_COUNT,
        "tasks": [
            {
                "id": TASK_ID,
                "title": "TASK_TITLE",
                "description": "TASK_DESCRIPTION",
                "deadline": "TASK_DEADLINE",
                "completed": "TASK_COMPLETED"  // 'Y' or 'N'
            },
            {
                "id": TASK_ID,
                "title": "TASK_TITLE",
                "description": "TASK_DESCRIPTION",
                "deadline": "TASK_DEADLINE",
                "completed": "TASK_COMPLETED"  // 'Y' or 'N'
            }
        ]
    }
}
```

### <a name="add-task"></a>Create a new task

| Label  | Value                                      |
| ------ |------------------------------------------- |
| Method | **<code>POST</code>**                      |
| URL    | /tasks-rest-api/v1/tasks                   |
| Header | Content-Type : application/json<br>Authorization : `ACCESS_TOKEN` |
| Body   | {<br>&nbsp;&nbsp;&nbsp;&nbsp;"title":`"TASK_TITLE"`,<br>&nbsp;&nbsp;&nbsp;&nbsp;"description":`"TASK_DESCRIPTION"`,&nbsp;&nbsp;&nbsp;&nbsp;*//optional*<br>&nbsp;&nbsp;&nbsp;&nbsp;"deadline":`"TASK_DEADLINE"`,&nbsp;&nbsp;&nbsp;&nbsp;*//optional, date format: 01/01/2019 01:00*<br>&nbsp;&nbsp;&nbsp;&nbsp;"completed":`"TASK_COMPLETED"`&nbsp;&nbsp;&nbsp;&nbsp;*// N - incompleted, Y - completed*<br>}|


The request contains user data: `ACCESS_TOKEN`, `TASK_TITLE`, `TASK_DESCRIPTION`, `TASK_DEADLINE` and `TASK_COMPLETED`.

**Response:**
```
{
    "statusCode": 201,
    "success": true,
    "messages": ["Task created"],
    "data": {
        "rows_returned": 1,
        "tasks": [
            {
                "id": TASK_ID,
                "title": "TASK_TITLE",
                "description": "TASK_DESCRIPTION",
                "deadline": "TASK_DEADLINE",
                "completed": "TASK_COMPLETED"  // 'Y' or 'N'
            }
        ]
    }
}
```

### <a name="get-tasks-by-page"></a>Get tasks by page number

| Label  | Value                                         |
| ------ |---------------------------------------------- |
| Method | **<code>GET</code>**                          |
| URL    | /tasks-rest-api/v1/tasks/page/`PAGE_NUMBER`   |
| Header | Authorization : `ACCESS_TOKEN`                |

The request contains user data: `PAGE_NUMBER` and `ACCESS_TOKEN`.

**Response:**
```
{
    "statusCode": 200,
    "success": true,
    "messages": [],
    "data": {
        "row_returned": TASKS_COUNT_ON_PAGE,
        "total_rows": TASKS_COUNT,
        "total_pages": TOTAL_PAGES_COUNT,
        "has_next_page": true|false,
        "has_previous_page": true|false,
        "tasks": [
            {
                "id": TASK_ID,
                "title": "TASK_TITLE",
                "description": "TASK_DESCRIPTION",
                "deadline": "TASK_DEADLINE",
                "completed": "TASK_COMPLETED"  // 'Y' or 'N'
            },
            {
                "id": TASK_ID,
                "title": "TASK_TITLE",
                "description": "TASK_DESCRIPTION",
                "deadline": "TASK_DEADLINE",
                "completed": "TASK_COMPLETED"  // 'Y' or 'N'
            }
        ]
    }
}
```

### <a name="get-completed-tasks"></a>Get list of completed tasks

| Label  | Value                                         |
| ------ |---------------------------------------------- |
| Method | **<code>GET</code>**                          |
| URL    | /tasks-rest-api/v1/tasks/complete             |
| Header | Authorization : `ACCESS_TOKEN`                |

The request contains user data: `ACCESS_TOKEN`.

**Response:**
```
{
    "statusCode": 200,
    "success": true,
    "messages": [],
    "data": {
        "row_returned": TASKS_COUNT,
        "tasks": [
            {
                "id": TASK_ID,
                "title": "TASK_TITLE",
                "description": "TASK_DESCRIPTION",
                "deadline": "TASK_DEADLINE",
                "completed": "TASK_COMPLETED"  // 'Y' or 'N'
            },
            {
                "id": TASK_ID,
                "title": "TASK_TITLE",
                "description": "TASK_DESCRIPTION",
                "deadline": "TASK_DEADLINE",
                "completed": "TASK_COMPLETED"  // 'Y' or 'N'
            }
        ]
    }
}
```

### <a name="get-incompleted-tasks"></a>Get list of incompleted tasks

| Label  | Value                                         |
| ------ |---------------------------------------------- |
| Method | **<code>GET</code>**                          |
| URL    | /tasks-rest-api/v1/tasks/incomplete           |
| Header | Authorization : `ACCESS_TOKEN`                |

The request contains user data: `ACCESS_TOKEN`.

**Response:**
```
{
    "statusCode": 200,
    "success": true,
    "messages": [],
    "data": {
        "row_returned": TASKS_COUNT,
        "tasks": [
            {
                "id": TASK_ID,
                "title": "TASK_TITLE",
                "description": "TASK_DESCRIPTION",
                "deadline": "TASK_DEADLINE",
                "completed": "TASK_COMPLETED"  // 'Y' or 'N'
            },
            {
                "id": TASK_ID,
                "title": "TASK_TITLE",
                "description": "TASK_DESCRIPTION",
                "deadline": "TASK_DEADLINE",
                "completed": "TASK_COMPLETED"  // 'Y' or 'N'
            }
        ]
    }
}
```

### <a name="get-task-by-id"></a>Get task by ID

| Label  | Value                                         |
| ------ |---------------------------------------------- |
| Method | **<code>GET</code>**                          |
| URL    | /tasks-rest-api/v1/tasks/`TASK_ID`            |
| Header | Authorization : `ACCESS_TOKEN`                |

The request contains user data: `TASK_ID` and `ACCESS_TOKEN`.

**Response:**
```
 "statusCode": 200,
    "success": true,
    "messages": [],
    "data": {
        "rows_returned": 1,
        "tasks": {
            "id": TASK_ID,
            "title": "TASK_TITLE",
            "description": "TASK_DESCRIPTION",
            "deadline": "TASK_DEADLINE",
            "completed": "TASK_COMPLETED"  // 'Y' or 'N'
        }
    }
```

### <a name="update-task-by-id"></a>Update task by ID

| Label  | Value                                         |
| ------ |---------------------------------------------- |
| Method | **<code>PATCH</code>**                        |
| URL    | /tasks-rest-api/v1/tasks/`TASK_ID`            |
| Header | Authorization : `ACCESS_TOKEN`                |
| Body   | {<br>&nbsp;&nbsp;&nbsp;&nbsp;"title":`"TASK_TITLE"`,<br>&nbsp;&nbsp;&nbsp;&nbsp;"description":`"TASK_DESCRIPTION"`,&nbsp;&nbsp;&nbsp;&nbsp;*//optional*<br>&nbsp;&nbsp;&nbsp;&nbsp;"deadline":`"TASK_DEADLINE"`,&nbsp;&nbsp;&nbsp;&nbsp;*//optional, date format: 01/01/2019 01:00*<br>&nbsp;&nbsp;&nbsp;&nbsp;"completed":`"TASK_COMPLETED"`&nbsp;&nbsp;&nbsp;&nbsp;*// N - incompleted, Y - completed*<br>}|

The request contains user data: `TASK_ID`, `ACCESS_TOKEN`, `TASK_TITLE`, `TASK_DESCRIPTION`, `TASK_DEADLINE` and `TASK_COMPLETED`..

**Response:**
```
{
    "statusCode": 200,
    "success": true,
    "messages": ["Task updated"],
    "data": {
        "rows_returned": 1,
        "tasks": {
            "id": TASK_ID,
            "title": "TASK_TITLE",
            "description": "TASK_DESCRIPTION",
            "deadline": "TASK_DEADLINE",
            "completed": "TASK_COMPLETED"  // 'Y' or 'N'
        }
    }
}
```

### <a name="delete-task-by-id"></a>Delete task by ID

| Label  | Value                                         |
| ------ |---------------------------------------------- |
| Method | **<code>DELETE</code>**                       |
| URL    | /tasks-rest-api/v1/tasks/`TASK_ID`            |
| Header | Authorization : `ACCESS_TOKEN`                |

The request contains user data: `TASK_ID` and `ACCESS_TOKEN`.

**Response:**
```
{
    "statusCode": 200,
    "success": true,
    "messages": ["Task deleted"],
    "data": null
}
```
