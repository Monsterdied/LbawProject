# EAP: Architecture Specification and Prototype

> This element organizes items associated with the high-level architectural plan of the information system under development, as well as the vertical prototype created to confirm the validity of the architecture.

## A7: Web Resources Specification

> This document provides a summary of the web resources intended for inclusion in the vertical prototype, categorized into modules. It's outlined the permissions applied to these modules, defining the access conditions for the resources. The web resources are described according to the OpenAPI standard, detailing the expected URL, supported HTTP methods, parameters, and potential responses for each resource.

### 1. Overview

| Modules | Description |
|---------|-------------|
| M01: Authentication | Web resources associated with user authentication, including system features such as login/logout and registration.|
| M02: User | Web resources associated with individual profile management, including system features such as view and edit personal profile information, view personal notifications and followed questions.|
| M03: Commentables & Comments| Web resources associated with questions, answers and comments, including system features such as add, view, vote, report and delete questions,answers and comments.|
| M04: Search | Web resources correspond to search features. Including searching users, questions with different types of filter and order. |
| M05: Administration | Web resources related to implementing terms of service enforcement, user blocking, unblocking, and banning, as well as functionalities for deleting posts/comments and updating static pages.|
| M06: Static Pages |Web resources with static content are in this module.|

*Table 62: QthenA Resources Overview*
### 2. Permissions

> This section defines the permissions used in the modules to establish the conditions of access to resources, in increasing order of restrictiveness.

| Identifier | Name | Description |
|------------|------|-------------|
| PUB | Public | An unauthenticated user. |
| USR | User | An authenticated user. |
| OWN | Own | User that are owners of the information (e.g. own profile, own comments, own anwsers, own questions) |
| MOD | Moderator | An moderator. |
| ADM | Administrator | Platform administrator. |

*Table 63: QthenA Permissions*

### 3. OpenAPI Specification

> OpenAPI specification in YAML format to describe the vertical prototype's web resources.

> Link to the `a7_openapi.yaml` file in the group's repository.


```yaml



openapi: 3.0.0
info:
 version: '1.0'
 title: 'LBAW QthenA API'
 description: 'Web Resources Specification (A7) for MediaLibrary'

servers:
- url: http://lbaw.fe.up.pt
  description: Production server


tags:
 - name: 'M01: Authentication'
 - name: 'M02: User'
 - name: 'M03: Commentables & Comments'
 - name: 'M04: Search'
 - name: 'M05: Administration'
 - name: 'M06: Static Pages'
paths:

  /login:
    get:
      operationId: R101
      summary: 'R101: Login Form'
      description: 'Provide login form. Access: PUB'
      tags:
        - 'M01: Authentication'
      responses:
        '200':
          description: 'Ok. Show Log-in UI'
    post:
      operationId: R102
      summary: 'R102: Login Action'
      description: 'Processes the login form submission. Access: PUB'
      tags:
        - 'M01: Authentication'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                email:          # <!--- form field name
                  type: string
                  format: email
                password:    # <!--- form field name
                  type: string
                  format: password
              required:
                - email
                - password

      responses:
        '302':
          description: 'Redirect after processing the login credentials.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: 'Successful authentication. Redirect to user profile.'
                  value: '/user/{id}'
                302Error:
                  description: 'Failed authentication. Redirect to login form.'
                  value: '/login'


  /register:
    get:
      operationId: R103
      summary: 'R103: Register Form'
      description: 'Provide register form. Access: PUB'
      tags:
        - 'M01: Authentication'
      responses:
        '200':
          description: 'Ok. Show Register UI'
    post:
      operationId: R104
      summary: 'R104: Register Action'
      description: 'Processes the register form submission. Access: PUB'
      tags:
        - 'M01: Authentication'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                name:          # <!--- form field name
                  type: string
                username:          # <!--- form field name
                  type: string
                email:          # <!--- form field name
                  type: string
                  format: email
                password:    # <!--- form field name
                  type: string
                  format: password
                confirm_password:    # <!--- form field name
                  type: string
                  format: password
              required:
                - name
                - username
                - email
                - password
                - confirm_password

      responses:
        '302':
          description: 'Redirect after processing the register credentials.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: 'Successful authentication. Redirect to user profile.'
                  value: '/user/{id}'
                302Error:
                  description: 'Failed resgister. Redirect to register form.'
                  value: '/register'

  /logout:

    post:
        operationId: R105
        summary: 'R105: Logout Action'
        description: 'Logout the current authenticated user. Access: USR, ADM'
        tags:
        - 'M01: Authentication'
        responses:
        '302':
            description: 'Redirect after processing logout.'
            headers:
            Location:
                schema:
                type: string
                examples:
                302Success:
                    description: 'Successful logout. Redirect to login form.'
                    value: '/login'
  /user/{id}:
    get:
      operationId: R201
      summary: 'R201: User'
      description: 'Provide User Profile. Access: USR'
      tags:
        - 'M02: User'
      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
      responses:
        '200':
          description: 'Ok. Show User Profile'  
        '302':
          description: 'Ok. Show Register UI' 

  /edituser/{id}:
    get:
      operationId: R202
      summary: 'R202: Edit Profile Form'
      description: 'Provide Edit Profile Form. Access: OWN ADM'
      tags:
        - 'M02: User'
      responses:
        '200':
          description: 'Ok. Show Edit Profile page UI'
        '302':
          description: 'Redirect to login user is not loged in.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Error:
                  description: 'User is not logged in. Redirect to login form.'
                  value: '/login'
  /updateuser/{id}/:
    post:
      operationId: R203
      summary: 'R203: Edit Profile Action'
      description: 'Edits the profile of the user. Access: OWN ADM'
      tags:
        - 'M02: User'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                name:          # <!--- form field name
                  type: string
                username:    # <!--- form field name
                  type: string
                email:    # <!--- form field name
                  type: string
                  format: email
                password:    # <!--- form field name
                  type: string
                  format: password
                bio:    # <!--- form field name
                  type: string

      responses:
        '302':
          description: 'Redirect after processing the Edit profile.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: 'Successful edition. Redirect to user profile.'
                  value: '/profile/{id}'
                302Error:
                  description: 'Failed edition. Redirect to login form.'
                  value: '/updateuser/{id}/'
############notifications##############
  /home/notifications:
    get:
      operationId: R204
      summary: 'R204: User notifications page.'
      description: 'Show user notifications page. Access: USR, ADM'
      tags:
        - 'M02: User'
      responses:
        '200':
          description: 'OK. Show the user notifications page.'
        '302':
          description: 'Redirect to login user is not loged in.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Error:
                  description: 'User is not logged in. Redirect to login form.'
                  value: '/login'
  /user/delete:
    post:
      operationId: R205
      summary: 'R205: Delete User Action'
      description: 'Delete an User from the database. Access: OWN, ADM'
      tags:
        - 'M02: User'
      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                id:          # <!--- form field name
                  type: integer
              required:
                - id
    responses:
      '302':
        description: 'Redirect after processing deletion of account.'
        headers:
          Location:
            schema:
              type: string
            examples:
              302Success:
                description: 'Successful deletion. Redirect to login form.'
                value: '/login'
              302Error:
                description: 'Not Successful deletion. Redirect to User profile.'
                value: '/user/{id}'
  /myquestions/{id}:
    get:
      operationId: R206
      summary: 'R206: Questions of the user'
      description: 'Returns the questions of the user. Access: USR'
      tags:
        - 'M02: User'
      responses:
        '200':
          description: Success
          content:
            application/json:
              schema:
                type: array
                items:
                  type: object
                  properties:
                    id:
                      type: string
                    title:
                      type: string
                    content:
                      type: string
                    date:
                      type: string
                    edited:
                      type: bool
                    votes:
                      type: int
                    author:
                      type: array
                    tags:
                      type: array
                example:
                  - id: 1
                    title: Rihanna - Unapologetic
                    content: Why is this music so bad i dont know how to deal with it.
                    date: "16/11/2022 22:13:3"
                    edited: true
                    votes: 15
                    author: ["Manuel Teixeira", "https//image.com/userid=2"]
                    tags: ["music", "pop"]
                  - id: 15
                    title: Ellon Musk - Tesla
                    content: Why is this car so bad i dont know how to deal with it.
                    date: "16/11/2022 22:13:3"
                    edited: true
                    votes: 19
                    author: ["Donald Trump", "https//image.com/userid=4"]
                    tags: ["cars", "tesla"]
  /myanswers/{id}:
    get:
      operationId: R207
      summary: 'R207: Answers of the user'
      description: 'Returns the Answer of the user. Access: USR'
      tags:
        - 'M02: User'
      responses:
        '200':
          description: Success
          content:
            application/json:
              schema:
                type: array
                items:
                  type: object
                  properties:
                    id:
                      type: string
                    content:
                      type: string
                    date:
                      type: string
                    edited:
                      type: bool
                    votes:
                      type: int
                example:
                  - id: 1
                    title: Rihanna - Unapologetic
                    content: Why is this music so bad i dont know how to deal with it.
                    date: "16/11/2022 22:13:3"
                    edited: true
                    votes: 15
                    author: ["Manuel Teixeira", "https//image.com/userid=2"]
                    tags: ["music", "pop"]
                  - id: 15
                    content: Why is this car so bad i dont know how to deal with it.
                    date: "16/11/2022 22:13:3"
                    edited: true
                    votes: 19
###############commentables##############
  /createquestion:
    get:
      operationId: R301
      summary: 'R301: Question Form'
      description: 'Provide Question Form. Access: USR'
      tags:
        - 'M03: Commentables & Comments'
      responses:
        '200':
          description: 'Ok. Show Edit Question page UI'
        '302':
          description: 'The user doesnt have permission.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Error:
                  description: 'User doesn t have permission to change this.'
                  value: '/question/{id}'
    post:
      operationId: R302
      summary: 'R302: Create Question Action'
      description: 'Create question. Access: USR'
      tags:
        - 'M03: Commentables & Comments'
      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                title:          # <!--- form field name
                  type: string
                content:    # <!--- form field name
                  type: string
                tags:
                  type: array
            required:
              - title
              - content
              - tags

      responses:
        '302':
          description: 'Redirect after processing the Edit question.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: 'Successful. Redirect to question page.'
                  value: '/question/{id}'
                302Error:
                  description: 'Failed . User is not logged in.'
                  value: '/createquestion'
  /question/{id}:
    get:
      operationId: R303
      summary: 'R303: User'
      description: 'Provide the question page. Access: USR'
      tags:
        - 'M03: Commentables & Comments'
      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
      responses:
        '200':
          description: 'Ok. Show Question Page'  
        '302':
          description: 'Redirect to login user is not loged in.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Error:
                  description: 'User is not logged in. Redirect to login form.'
                  value: '/login'
  /question/{id}/edit:
    get:
      operationId: R304
      summary: 'R304: Edit Question Form'
      description: 'Provide Edit Question Form. Access: OWN ADM'
      tags:
        - 'M03: Commentables & Comments'
      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
      responses:
        '200':
          description: 'Ok. Show Edit Question page UI'
        '302':
          description: 'The user doesnt have permission.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Error:
                  description: 'User doesn t have permission to change this.'
                  value: '/question/{id}'
    post:
      operationId: R305
      summary: 'R305: Edit Question Action'
      description: 'Edits the question. Access: OWN ADM'
      tags:
        - 'M03: Commentables & Comments'
      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                title:          # <!--- form field name
                  type: string
                content:    # <!--- form field name
                  type: string
                tags:
                  type: array
            required:
              - title
              - content
              - tags

      responses:
        '302':
          description: 'Redirect after processing the Edit question.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: 'Successful edition. Redirect to question page.'
                  value: '/question/{id}'
                302Error:
                  description: 'Failed edition. Redirect to question page form.'
                  value: '/question/{id}'
  /question/{id}/delete:
    post:
      operationId: R306
      summary: 'R306: Delete Question Action'
      description: 'Delete a Question from the database. Access: OWN, ADM'
      tags:
        - 'M03: Commentables & Comments'
      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
      responses:
        '302':
          description: 'Redirect after processing deletion of question.'
          headers:
          Location:
            schema:
              type: string
            examples:
              302Success:
                description: 'Successful edition. Redirect to question page.'
                value: '/question/{id}'
              302Error:
                description: 'Failed edition. Redirect to question page form.'
                value: '/question/{id}'
  /commentable/{id}/comment:
    get:
      operationId: R307
      summary: 'R307: comment form'
      description: 'Provide comment form. Access: USR'
      tags:
        - 'M03: Commentables & Comments'
      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
      responses:
        '200':
          description: 'Ok. Show User Comment form'  
        '302':
          description: 'Ok. Show Register UI' 
    post:
      operationId: R308
      summary: 'R308: Comment Question Action'
      description: 'Comment a Question. Access: USR'
      tags:
        - 'M03: Commentables & Comments'
      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                content:          # <!--- form field name
                  type: string
            required:
              - content

      responses:
        '302':
          description: 'Redirect after processing the Comment.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: 'Successful comment. Redirect to question page.'
                  value: '/question/{id}'
                302Error:
                  description: 'Failed comment. Redirect to question page form.'
                  value: '/question/{id}'
  /commentable/{id}/comment/{id_comment}/edit:
    get:
      operationId: R309
      summary: 'R309: Edit Comment Form'
      description: 'Provide Edit Comment Form. Access: OWN ADM'
      tags:
        - 'M03: Commentables & Comments'
      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
        - in: path
          name: id_comment
          schema:
            type: integer
          required: true
      responses:
        '200':
          description: 'Ok. Show Edit Comment page UI'
        '302':
          description: 'The user doesnt have permission.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Error:
                  description: 'User doesn t have permission to change this.'
                  value: '/question/{id}'
    post:
      operationId: R310
      summary: 'R310: Edit Comment Action'
      description: 'Edit a Comment. Access: OWN ADM'
      tags:
        - 'M03: Commentables & Comments'
      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
        - in: path
          name: id_comment
          schema:
            type: integer
          required: true
      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                content:          # <!--- form field name
                  type: string
            required:
              - content

      responses:
        '302':
          description: 'Redirect after processing the Edit.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: 'Successful edition. Redirect to question page.'
                  value: '/question/{id}'
                302Error:
                  description: 'Failed edition. Redirect to question page form.'
                  value: '/question/{id}'

  /commentable/{id}/comment/{id_comment}/delete:
    post:
      operationId: R311
      summary: 'R311: Delete Comment Action'
      description: 'Delete a Comment from the database. Access: OWN, ADM'
      tags:
        - 'M03: Commentables & Comments'
      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
        - in: path
          name: id_comment
          schema:
            type: integer
          required: true
      responses:
        '302':
          description: 'Redirect after processing deletion of comment.'
          headers:
          Location:
            schema:
              type: string
            examples:
              302Success:
                description: 'Successful edition. Redirect to question page.'
                value: '/question/{id}'
              302Error:
                description: 'Failed edition. Redirect to question page form.'
                value: '/question/{id}'
  /question/{id}/answer:
    get:
      operationId: R312
      summary: 'R312: Answer form'
      description: 'Provide User Answer form. Access: USR'
      tags:
        - 'M03: Commentables & Comments'
      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
      responses:
        '200':
          description: 'Ok. Show User Answer form'  
        '302':
          description: 'Ok. Show Register UI' 
    post:
      operationId: R313
      summary: 'R313: Answer Question Action'
      description: 'Answer a Question. Access: USR'
      tags:
        - 'M03: Commentables & Comments'
      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                content:          # <!--- form field name
                  type: string
            required:
              - content

      responses:
        '302':
          description: 'Redirect after processing the Answer.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: 'Successful answer. Redirect to question page.'
                  value: '/question/{id}'
                302Error:
                  description: 'Failed answer user is not logged in. Redirect to login page form.'
                  value: '/login'
  /question/{id}/answer/{id_answer}/edit:
    get:
      operationId: R314
      summary: 'R314: Edit Answer Form'
      description: 'Provide Edit Answer Form. Access: OWN ADM'
      tags:
        - 'M03: Commentables & Comments'
      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
        - in: path
          name: id_comment
          schema:
            type: integer
          required: true
      responses:
        '200':
          description: 'Ok. Show Edit Comment page UI'
        '302':
          description: 'The user doesnt have permission.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Error:
                  description: 'User doesn t have permission to change this.'
                  value: '/question/{id}'
    post:
      operationId: R315
      summary: 'R315: Edit Answer Action'
      description: 'Edit an Answer. Access: OWN ADM'
      tags:
        - 'M03: Commentables & Comments'
      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
        - in: path
          name: id_answer
          schema:
            type: integer
          required: true
      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                content:          # <!--- form field name
                  type: string
            required:
              - content

      responses:
        '302':
          description: 'Redirect after processing the Edit.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: 'Successful edition. Redirect to question page.'
                  value: '/question/{id}'
                302Error:
                  description: 'Failed edition. Redirect to question page form.'
                  value: '/question/{id}'
  /question/{id}/answer/{id_answer}/delete:
    post:
      operationId: R316
      summary: 'R316: Delete Answer Action'
      description: 'Delete an Answer from the database. Access: OWN, ADM'
      tags:
        - 'M03: Commentables & Comments'
      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: true
        - in: path
          name: id_answer
          schema:
            type: integer
          required: true
      responses:
        '302':
          description: 'Redirect after processing deletion of answer.'
          headers:
          Location:
            schema:
              type: string
            examples:
              302Success:
                description: 'Successful edition. Redirect to question page.'
                value: '/question/{id}'
              302Error:
                description: 'Failed edition. Redirect to question page form.'
                value: '/question/{id}'
 

  /search/question/:
    get:
      operationId: R401
      summary: 'R401: Search for the questions'
      description: 'Searches for questions. Access: USR ADM.'

      tags: 
        - 'M04: Search'

      parameters:
        - in: query
          name: query
          description: String to use for exact-match
          schema:
            type: string
          required: false
        - in: query
          name: tag
          description: A tag that must be present in the questions shown
          schema:
            type: string
          required: false
        - in: query
          name: Answered
          description: Boolean with the Answered flag value if true the answer was already reasolved
          schema:
            type: boolean
          required: false
        - in: query
          name: OrderedBy
          description: Correspond to one of the order selected by the user
          schema:
            type: string
          required: false

      responses:
        '200':
          description: Success
          content:
            application/json:
              schema:
                type: array
                items:
                  type: object
                  properties:
                    id:
                      type: string
                    title:
                      type: string
                    content:
                      type: string
                    date:
                      type: string
                    edited:
                      type: bool
                    votes:
                      type: int
                    author:
                      type: array
                    tags:
                      type: array
                example:
                  - id: 1
                    title: Rihanna - Unapologetic
                    content: Why is this music so bad i dont know how to deal with it.
                    date: "16/11/2022 22:13:3"
                    edited: true
                    votes: 15
                    author: ["Manuel Teixeira", "https//image.com/userid=2"]
                    tags: ["music", "pop"]
                  - id: 15
                    title: Ellon Musk - Tesla
                    content: Why is this car so bad i dont know how to deal with it.
                    date: "16/11/2022 22:13:3"
                    edited: true
                    votes: 19
                    author: ["Donald Trump", "https//image.com/userid=4"]
                    tags: ["cars", "tesla"]

  /questions/:
    get:
      operationId: R402
      summary: 'R402: Search for the questions by the author'
      description: 'Searches for questions. Access: USR ADM.'

      tags: 
        - 'M04: Search'

      parameters:
        - in: query
          name: query
          description: String to use for exact-match
          schema:
            type: string
          required: false
        - in: query
          name: tag
          description: A tag that must be present in the questions shown
          schema:
            type: string
          required: false
        - in: query
          name: Answered
          description: Boolean with the Answered flag value if true the answer was already reasolved
          schema:
            type: boolean
          required: false
        - in: query
          name: OrderedBy
          description: Correspond to one of the order selected by the user
          schema:
            type: string
          required: false

      responses:
        '200':
          description: Success
          content:
            application/json:
              schema:
                type: array
                items:
                  type: object
                  properties:
                    id:
                      type: string
                    title:
                      type: string
                    content:
                      type: string
                    date:
                      type: string
                    edited:
                      type: bool
                    votes:
                      type: int
                    author:
                      type: array
                    tags:
                      type: array
                example:
                  - id: 1
                    title: Rihanna - Unapologetic
                    content: Why is this music so bad i dont know how to deal with it.
                    date: "16/11/2022 22:13:3"
                    edited: true
                    votes: 15
                    author: ["Manuel Teixeira", "https//image.com/userid=2"]
                    tags: ["music", "pop"]
                  - id: 15
                    title: Ellon Musk - Tesla
                    content: Why is this car so bad i dont know how to deal with it.
                    date: "16/11/2022 22:13:3"
                    edited: true
                    votes: 19
                    author: ["Donald Trump", "https//image.com/userid=4"]
                    tags: ["cars", "tesla"]

  /search/users/:
    get:
      operationId: R403
      summary: 'R403: Search for the users'
      description: 'Searches for users. Access: USR ADM.'

      tags: 
        - 'M04: Search'

      parameters:
        - in: query
          name: query
          description: String to use for exact-match
          schema:
            type: string
          required: false

      responses:
        '200':
          description: Success
          content:
            application/json:
              schema:
                type: array
                items:
                  type: object
                  properties:
                    id:
                      type: string
                    name:
                      type: string
                    username:
                      type: string
                    picture:
                      type: string
                    badges:
                      type: array
                example:
                  - id: 1
                    name: Manuel Teixeira
                    username: manuelteixeira
                    picture: https//image.com/userid=2
                    badges: ["gold", "silver"]
                  - id: 15
                    name: Donald Trump
                    username: donaldtrump
                    picture: https//image.com/userid=4
                    badges: ["gold", "silver","platinum"]







```


## A8: Vertical prototype

> Brief presentation of the artifact goals.

### 1. Implemented Features

#### 1.1. Implemented User Stories

> Identify the user stories that were implemented in the prototype.  

| User Story reference|Name|Priority|Description|
| -------------------- | ---------------------- | -------------------------- | ----------------------------- |
|US01|View Top Questions|High|As a User, I want to be able to view the top questions on the app, so that it allows me to quickly find the most popular questions.|
|US02|View Recent Questions|High|As a User, I want to be able to view the most recent questions on the app, so that it enables me to stay updated with the latest discussions within the community.|
|US03|Browse Questions|High|As a User, I want the ability to easily browse and explore questions on the app, so that it allows me to discover a range of topics and find questions that interest me.|
|US04|Log-in the System|High|As a Visitor, I want the ability to log in of my user account, so that it allows me to see more information and get acess to more features.|
|US05|Register in the System|High|As a Visitor, I want the ability to register for a User account, so that it allows me to access personalized features, interact with the community, and maintain my profile.|
|US06|Logout|High|As a Authenticated User, I want the ability to log out of my user account, so that it will secure my account when I'm not using the app.|
|US07|View Profile|High|As an Authenticated User, I want to be able to view my own profile as well of other users, so that it allows me to see and manage my own information, and learn more about other members of the app.|
|US08|Edit Profile|High|As an Authenticated User, I want to be able to edit my own profile, so that it allows me to update my information.|
|US09|Search Exact Match|High|As an Authenticated User, I want to be able to perform an exact match search on the app, so that I can find specific questions, answers, or user profiles with precision.|
|US10|View Personal Feed|High|As an Authenticated User, I want to be able to view my personalized feed on the app, so that I can stay updated with content and discussions that are relevant to my interests and recent activity.|
|US11|Post Question|High|As an Authenticated User, I want to be able to post questions on the app, so that I can initiate discussions and contribute to the community.|
|US12|Post Answer|High|As an Authenticated User, I want to be able to post answers to questions on the app, so that I can provide helpful information, contribute to discussions, and assist other community members.|
|US13|View My Questions|High|As an Authenticated User, I want to have the ability to view a list of all the questions I've posted, so that I can easily access and manage the questions I've asked.|
|US14|View My Answers|High|As an Authenticated User, I want to have the ability to view a list of all the answers I've posted, so that I can easily access and manage the answers I've provided.|
|US15|Edit Question|High|As a Author, I want to edit my question, so that I can correct spelling or grammar mistakes and explain the question better| 
|US16|Delete Question|High|As a Author, I want to delete my question, so that I can remove a question I placed by mistake| 
|US17|Edit Answer|High|As a Author, I want to edit my answer previously, so that I can correct spelling or grammar mistakes and explain the answer better| 
|US18|Delete Answer|High|As a Author, I want to delete my answer, so that I can remove a answer I made by mistake| 
|US19|Administer User Accounts (search, view, edit, create)|High|As a Administrator, I want to administrate user accounts|
|US20|View Question Details|Medium|As a User, I want the ability to view the full details of a question on the app, so that it allows me to access all information and answers related to a specific question.|
|US21|View User Profiles|Medium|As a User, I want the ability to view the profiles of other users on the app, so that it allows me to learn more about the members of the community.|
|US22|Delete Account|Medium|As an Authenticated User, I want to have the ability to delete my user account on the app, so that I can permanently remove my presence and  anonymize data from the app.|
|US23|Search Filters|Medium|As an Authenticated User, I want to have the ability to apply various filters when performing searches on the app, so that I can refine my search results based on specific filters.|
|US24|Comment on Questions|Medium|As an Authenticated User, I want to be able to give comments on questions, so that I can express my ideas and opinions via text.|
|US25|Comment on Answers|Medium|As an Authenticated User, I want to be able to give comments on questions, so that I can express my ideas and opinions via text.|
|US26|Placeholders in Form Inputs|Medium|As an Authenticated User, I want to have descriptive placeholders within various forms and input fields so that I can easily understand the information expected or how to fill out each field.|
|US27|Contextual Error Messages|Medium|As an Authenticated User, I want to receive contextual error messages when I encounter issues or make mistakes while using the app, so that I can understand better what went wrong.|
|US28|Edit Comment|Medium|As a Author, I want to edit my comment, so that I can correct spelling or grammar mistakes or even change it|
|US29|Delete Comment|Medium|As a Author, I want to delete my comment, so that I can remove a comment I made by mistake|
|US30|Delete Content|Medium|As a Moderator I want to be able to delete content, so that I can delete content that does not follow our guidelines|
|US31|Administrator Accounts|Medium|As a Administrator I want to have administrator account, so that it helps me administrate the platform with administrative privileges|
|US32|Delete User Account|Medium|As a Administrator I want to delete an account, so that I can remove a User that does not follow the guidelines|
|US33|Support Profile Picture|Low|As an Authenticated User, I want to have the ability to set and display a profile picture on my user profile, so that I can personalize my presence on the app and make it more unique.|
|US34|Ordering of the results|Low|As an Authenticated User, I want to have the ability to customize the ordering of search results and lists of content on the app, so that I can quickly find the content I'm searching for.|
|US35|Support User Badges|Low|As an Authenticated User, I want to be able to earn and display user badges based on my contributions and activities within the community, so that I can get recognized for my achievements and increasing my reputation.|
|US36|Donations|Low|As an Authenticated User, I want to be able to make donations to other users within the application, so that I can express my appreciation and support for their valuable contributions.|

*Table 64: User Stories Implemented*




#### 1.2. Implemented Web Resources

> Identify the web resources that were implemented in the prototype.  

> Module M01: Authentication

| Web Resource Reference | URL                            |
| ---------------------- | ------------------------------ |
| R101: Login Form | GET[/login](https://lbaw2357.lbaw.fe.up.pt/login) |
| R102: Login Action | POST/login|
| R103: Register Form | GET[/register](https://lbaw2357.lbaw.fe.up.pt/register)|
| R104: Register Action | POST/register|
|R105: Logout Action| POST/logout|


> Module M02: User

| Web Resource Reference | URL                            |
| ---------------------- | ------------------------------ |
|R201: User| GET[/profile/{id}](https://lbaw2357.lbaw.fe.up.pt/profile/21) |
|R202: Edit Profile Form  | GET[/editprofile](https://lbaw2357.lbaw.fe.up.pt/editprofile/21) |
|R203: Edit Profile Action| POST/edituser/{id} |
|R204: Delete User Action|  POST/user/{id}/delete|
|R205: Questions of the user| GET[/myquestions/{id}](https://lbaw2357.lbaw.fe.up.pt/myquestions/21)|
|R207: Answers of the user|GET[/myanswers/{id}](https://lbaw2357.lbaw.fe.up.pt/myanswers/21)|

> Module M03: Commentables & Comments

| Web Resource Reference | URL                            |
| ---------------------- | ------------------------------ |
| R301: Question Form| GET[/createquestion](https://lbaw2357.lbaw.fe.up.pt/createquestion)|
| R302: Create Question Action|POST/createquestion|
| R303: User | GET[/question](https://lbaw2357.lbaw.fe.up.pt/question/1)|
|R304: Edit Question Form| GET[/question/{id}/edit](https://lbaw2357.lbaw.fe.up.pt/question/1/edit)|
|R305: Edit Question Action|POST/question/{id}/edit|
|R306: Delete Question Action|POST/question/{id}/delete|
|R307: Comment Form|GET[/commentable/{id}/comment](https://lbaw2357.lbaw.fe.up.pt/commentable/21/comment)|
|R308: Comment Question Action|POST/commentable/{id}/comment|
|R309: Edit Comment Form|GET[/commentable/{id}/comment/{id}/edit](https://lbaw2357.lbaw.fe.up.pt/commentable/21/comment/3/edit)| 
|R310: Edit Comment Action|POST/commentable/21/comment/3/edit|
|R311: Delete Comment Action|POST/commentable/{id}/comment/{id_comment}/delete|
|R312: Answer Form|GET[/question/{id}/answer](https://lbaw2357.lbaw.fe.up.pt/question/2/answer)|
|R313: Answer Question Action|POST/question/{id}/answer|
|R314: Edit Answer Form|GET[/question/{id}/answer/{id}/edit](https://lbaw2357.lbaw.fe.up.pt/question/2/answer/24/edit)|
|R315: Edit Answer Action|POST/question/2/answer/24/edit|
|R316: Delete Answer Action|POST/question/{id}/answer/{id_answer}/delete|

> Module M04: Search

| Web Resource Reference | URL                            |
| ---------------------- | ------------------------------ |
| R401: Search Questions | GET[/search/question/byQuestion](https://lbaw2357.lbaw.fe.up.pt/questions)|
|R403: Search Users|GET[/search/user](https://lbaw2357.lbaw.fe.up.pt/users)|

> Module M05: Administration

| Web Resource Reference | URL                            |
| ---------------------- | ------------------------------ |
| R501: Edit Profile with Admin Form | GET[/editprofile/{id}](https://lbaw2357.lbaw.fe.up.pt/editprofile/11) |
| R502: Edit Profile with Admin Action | POST/EditProfileWithAdminAction|


> Module M06: Static Pages

| Web Resource Reference | URL                            |
| ---------------------- | ------------------------------ |
| R01: Web resource name | URL to access the web resource |

### 2. Prototype

We directed our efforts towards developing the high prority features of the project for this prototype, and then the medium ones. There was some emphasis  on the visual aesthetics, resulting in an mostly imperfect design. However, it provides a basic understanding of the layout and ensures easy navigation through the website.

Access the prototype via http://lbaw2357.lbaw.fe.up.pt.

Credentials : Email -> admin@example.com ; Password -> 1234
---


## Revision history

Changes made to the first submission:


***
GROUP2357, 22/11/2023
 
* Group member 1 Diogo Sarmento, up202109663@fe.up.pt (editor of A7/A8)
* Group member 2 Rodrigo Povoa , up202108890@fe.up.pt (editor of A7/A8)
* Group member 3 Tomás Sarmento, up202108778@fe.up.pt (editor of A7/A8)