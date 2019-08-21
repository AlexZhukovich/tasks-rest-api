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
  * **<code>GET</code>** Get tasks based on logged in user ([see requirements](#get-tasks)). 
  * **<code>POST</code>** Create a new task for the logged in user ([see requirements](#add-task)).
* **/v1/tasks/page/PAGE**
  * **<code>GET</code>** Get tasks based on logged in user for the `PAGE_NUMBER` ([see requirements](#get-tasks-by-page)).
* **/v1/tasks/complete**
  * **<code>GET</code>** Get list of completed tasks for logged in user ([see requirements](#get-completed-tasks)). 
* **/v1/tasks/incomplete**
  * **<code>GET</code>** Get list of incompleted tasks for logged in user([see requirements](#get-incompleted-tasks)).
* **/v1/tasks/TASK_ID**
  * **<code>GET</code>** Get tasks by `TASK_ID`, based on logged in user ([see requirements](#get-task-by-id)).
  * **<code>PATCH</code>** Update task by `TASK_ID`, based on logged in user ([see requirements](#update-task-by-id)).
  * **<code>DELETE</code>** Delete task by `TASK_ID`, based on logged in user ([see requirements](#delete-task-by-id)).

## Request requirements 
Description of basic requirements for each request.

### <a name="create_user_account"></a>Create a new user account

| Label  | Value                           |
| ------ |-------------------------------- |
| Method | **<code>POST</code>**           |
| URL    | /tasks-rest-api/v1/users        |
| Header | Content-Type : application/json |
| Body   | {<br>&nbsp;&nbsp;&nbsp;&nbsp;"fullname":"FULL_NAME", <br>&nbsp;&nbsp;&nbsp;&nbsp;"username":"USER_NAME", <br>&nbsp;&nbsp;&nbsp;&nbsp;"password":"PASSWORD"<br>}|

### <a name="create_user_session"></a>Create a new user session

| Label  | Value                           |
| ------ |-------------------------------- |
| Method | **<code>POST</code>**           |
| URL    | /tasks-rest-api/v1/sessions     |
| Header | Content-Type : application/json |
| Body   | {<br>&nbsp;&nbsp;&nbsp;&nbsp;"username":"USER_NAME", <br>&nbsp;&nbsp;&nbsp;&nbsp;"password":"PASSWORD"<br>}|

### <a name="refresh-tokens"></a>Refresh access and refresh tokens

| Label  | Value                                      |
| ------ |------------------------------------------- |
| Method | **<code>PATCH</code>**                     |
| URL    | /tasks-rest-api/v1/sessions/**SESSION_ID** |
| Header | Content-Type : application/json<br>Authorization : ACCESS_TOKEN |
| Body   | {<br>&nbsp;&nbsp;&nbsp;&nbsp;"refresh_token":"REFRESH_TOKEN"<br>}|

### <a name="delete_existing_session"></a>Delete existing session

| Label  | Value                                      |
| ------ |------------------------------------------- |
| Method | **<code>DELETE</code>**                    |
| URL    | /tasks-rest-api/v1/sessions/**SESSION_ID** |
| Header | Authorization : ACCESS_TOKEN               |

### <a name="get-tasks"></a>Get tasks based on logged in user

| Label  | Value                                      |
| ------ |------------------------------------------- |
| Method | **<code>GET</code>**                       |
| URL    | /tasks-rest-api/v1/tasks                   |
| Header | Authorization : ACCESS_TOKEN               |

### <a name="add-task"></a>Create a new task for the logged in user

| Label  | Value                                      |
| ------ |------------------------------------------- |
| Method | **<code>POST</code>**                      |
| URL    | /tasks-rest-api/v1/tasks                   |
| Header | Content-Type : application/json<br>Authorization : ACCESS_TOKEN |
| Body   | {<br>&nbsp;&nbsp;&nbsp;&nbsp;"title":"New Task",<br>&nbsp;&nbsp;&nbsp;&nbsp;"description":"New task description",&nbsp;&nbsp;&nbsp;&nbsp;*//optional*<br>&nbsp;&nbsp;&nbsp;&nbsp;"deadline":"01/01/2019 01:00",&nbsp;&nbsp;&nbsp;&nbsp;*//optional*<br>&nbsp;&nbsp;&nbsp;&nbsp;"completed":"N"&nbsp;&nbsp;&nbsp;&nbsp;*// N - incompleted, Y - completed*<br>}|


### <a name="get-tasks-by-page"></a>Get tasks based on logged in user for the `PAGE_NUMBER`

Each page contains 20 tasks. It can be modified in the source code.

| Label  | Value                                         |
| ------ |---------------------------------------------- |
| Method | **<code>GET</code>**                          |
| URL    | /tasks-rest-api/v1/tasks/page/**PAGE_NUMBER** |
| Header | Authorization : ACCESS_TOKEN                  |


### <a name="get-completed-tasks"></a>Get list of completed tasks for logged in user

| Label  | Value                                         |
| ------ |---------------------------------------------- |
| Method | **<code>GET</code>**                          |
| URL    | /tasks-rest-api/v1/tasks/complete             |
| Header | Authorization : ACCESS_TOKEN                  |

### <a name="get-incompleted-tasks"></a>Get list of incompleted tasks for logged in user

| Label  | Value                                         |
| ------ |---------------------------------------------- |
| Method | **<code>GET</code>**                          |
| URL    | /tasks-rest-api/v1/tasks/incomplete           |
| Header | Authorization : ACCESS_TOKEN                  |


### <a name="get-task-by-id"></a>Get task by `TASK_ID`, based on logged in user

| Label  | Value                                         |
| ------ |---------------------------------------------- |
| Method | **<code>GET</code>**                          |
| URL    | /tasks-rest-api/v1/tasks/**TASK_ID**          |
| Header | Authorization : ACCESS_TOKEN                  |


### <a name="update-task-by-id"></a>Update task by TASK_ID, based on logged in user

| Label  | Value                                         |
| ------ |---------------------------------------------- |
| Method | **<code>PATCH</code>**                        |
| URL    | /tasks-rest-api/v1/tasks/**TASK_ID**          |
| Header | Authorization : ACCESS_TOKEN                  |
| Body   | {<br>&nbsp;&nbsp;&nbsp;&nbsp;"title":"New Task",<br>&nbsp;&nbsp;&nbsp;&nbsp;"description":"New task description",&nbsp;&nbsp;&nbsp;&nbsp;*//optional*<br>&nbsp;&nbsp;&nbsp;&nbsp;"deadline":"01/01/2019 01:00",&nbsp;&nbsp;&nbsp;&nbsp;*//optional*<br>&nbsp;&nbsp;&nbsp;&nbsp;"completed":"N"&nbsp;&nbsp;&nbsp;&nbsp;*// N - incompleted, Y - completed*<br>}|


### <a name="delete-task-by-id"></a>Delete task by TASK_ID, based on logged in user

| Label  | Value                                         |
| ------ |---------------------------------------------- |
| Method | **<code>DELETE</code>**                       |
| URL    | /tasks-rest-api/v1/tasks/**TASK_ID**          |
| Header | Authorization : ACCESS_TOKEN                  |
