# PA: Product and Presentation

## A9: Product

> Q&A functions as an online discussion hub catering to both seasoned developers and newcomers, fostering knowledge exchange on various topics.

> Within Q&A, users have the liberty to elaborate questions linked to specific categories. Users also possess the ability to contribute answers to questions and engage in discussions via comments. To ensure a self-regulating community, the platform employs a system where users can vote (up or down) on questions or answers, and report any inappropriate content. Community members who garner trust through their reputation points are elegible to gain certain badges and the possibility to become a moderator, which can moderate all content, including editing and deleting others' posts. The platform offers advanced search functionalities, employing matching to scour both question titles and content, and enabling users to refine their searches by filtering through categories.

> This artifact serves to outline the functionalities embedded within our platform, in addition to providing insights into the developmental specifics of the product.

### 1\. Installation

> Link to the release with the final version of the source code in the group's Git repository.\
> Full Docker Command: docker run -it -p 8000:80 --name=lbaw2357 -e DB_DATABASE="lbaw2357" -e DB_SCHEMA="lbaw2357" -e DB_USERNAME="lbaw2357" -e DB_PASSWORD="EYrQzbEb" git.fe.up.pt:5050/lbaw/lbaw2324/lbaw2357

### 2\. Usage

> URL to the product: https://lbaw2357.lbaw.fe.up.pt/

#### 2.1. Administration Credentials

| Email | Password |
|-------|----------|
| admin@example.com | 1234 |
| moderator@example.com | 12345678 |
| dragon29r@gmail.com | 12345678 |

_Table 71: Administration credentials_

#### 2.2. User Credentials

| Type | Username | Password |
|------|----------|----------|
| Authenticated User | kanderson@example.net | 12345678 |

_Table 72: User credentials_

### 3\. Application Help

> For starters, whenever anything goes wrong, there will be displayed a message error explaining the reason for the error and how to handle it. For example, if a user wants to create another account unblock request, instead of creating another request, it will display 'Unblock request already exists.'

> Our approach to navigation is to ensure uniform user experience across every page by maintaining consistent layouts for the navigation bar, sidebar and footer, along with standardized color schemes throughout the site.

> Finally, we also implemented icons in certain buttons/links instead of the text, to facilitate the interpretation and readibility. Not only that, but whenever I want to see the user's profile of a question, I can simply press the username, and it will redirect to it's profile.

### 4\. Input Validation

> According to our implementation, additionally to using the laravel built-in functions, we also created some php function to validate the inputs. For example, we ask for a minimum of length 8 for the password, or we ask for a minimum of 16 in content of the question we require a unique username and a unique email as well as every tag has an unique name.

### 5\. Check Accessibility and Usability

> Accessibility: [Accessibility](https://git.fe.up.pt/lbaw/lbaw2324/lbaw2357/-/blob/main/reports/acessibilidade.pdf?ref_type=heads)
>
> Usability: [Usability](https://git.fe.up.pt/lbaw/lbaw2324/lbaw2357/-/blob/main/reports/usabilidade.pdf?ref_type=heads)

### 6\. HTML & CSS Validation

> HTML: [HTML](https://git.fe.up.pt/lbaw/lbaw2324/lbaw2357/-/tree/main/reports/html?ref_type=heads)
>
> CSS: [CSS](https://git.fe.up.pt/lbaw/lbaw2324/lbaw2357/-/blob/main/reports/css/css.pdf?ref_type=heads)

### 7\. Revisions to the Project

> As we expected, our project suffered major changes since the submission of the er:

#### 7.1 Database

> There were a lot of changes in the database, from the tables itself to the populate, so we thougth that we should resume the most important ones:
>
> * Added 2 new attributes to the user table : _blocked_ and _remember_token_ (for block actions and recover password, respectively. Removed some required's and not null's from the table.
> * Removed some double primary keys from some tables , such as BadgeAttainment. We eventually found a piece of code that could force Laravel to work with double primary keys
> * Added 2 new attributes to the content table : _deleted_ and _blocked_.
> * Changed most tables named id's to id. For example, in the _Question_ table, we changed the id from commentable_id to id.
> * Created a new table named _UnblockAccount_. Used for unblock account apeals that the users do.
> * Removed _select_correct_answer,_ _update_points, generate_answer_notification, generate_comment_notification_ triggers from the database and implemented these in the php.
> * Removed _prevent_self_vote_ trigger as it was the complete opposite of what the business rules asked.
> * Removed _question_minimum_tag_ trigger.
> * Changed the old populate by other one that has way more data and it's slighty more realistic. The following python code is the one that generated the database:
>
> ```python
> from faker import Faker
> import random
> fake = Faker()
> random.seed(0)
> nnames = 1000
> nquestions = 1000
> ncomments = 10000
> nanswers = 10000
> variation_days = 90
> f = open("1.txt", "w")
> f.write("INSERT INTO AppUser (name, username, email, password, bio, profilepicture, usertype) \n VALUES \n")
> names = []
> emails = []
> content_user = {}
> nnotification = 1
> for i in range(nnames):
>     user_name = fake.name()
>     while(user_name in names):
>         user_name = fake.name()
>     names.append(user_name)
>     user_email = fake.email()
>     while(user_email in emails):
>         user_email = fake.email()
>     emails.append(user_email)
>     f.write("('" + fake.name() + "','" + user_name +"','" + user_email+ "'," + "'password'" + ",'" + fake.sentence() + "','NcIkXUq1IpkhshOeSYHMyDmX6u0q7Deku5FNMiWv.png','user')")
>     if(i < nnames-1):
>         f.write(",\n")
> f.write(";\n")
> for i in range(nquestions):
>     f.write("INSERT INTO Content (user_id, content ,date , edited)\nVALUES\n")
>     edited = "false"
>     if(random.random() < 0.5):
>         edited = "true"
>     author_id = int((nnames - 1)*random.random() + 1)
>     f.write("(" + str(author_id) + ",'" + fake.text()+ "'," "NOW() - INTERVAL '" + str(int((50 - 1)*random.random() + 1)) + " days'," +edited+ ");\n")
>     f.write("INSERT INTO Commentable (id)\nVALUES")
>     index = i + 1
>     f.write("("+ str(index) + ");\n")
>     f.write("INSERT INTO Question (id, title , correct_answer_id)\nVALUES")
>     f.write("("+ str(index)+",'"+ fake.sentence()[:-1] +"?',"+"NULL"+");\n")
>     content_user[index] = author_id
>     ntags = random.randrange(1, 5)
>     tags = random.sample(range(1, 20), ntags)
>     f.write("INSERT INTO QuestionTag (question_id, tag_id)\nVALUES\n")
>     for tag in tags:
>         f.write("("+ str(index)+","+ str(tag) + ")")
>         if(tag != tags[-1]):
>             f.write(",\n")
>     f.write(";\n")
>     nvotes = random.randrange(2, 50)
>     votes = random.sample(range(1, nnames), nvotes)
>     f.write("INSERT INTO Vote (user_id, content_id, vote)\nVALUES\n")
>     votec = 0
>     for vote in votes:
>         like = "false"
>         if(author_id == vote):
>             continue
>         if(random.random() < 0.8):
>             like = "true"
>         if(votec != 0):
>             f.write(",\n")
>         f.write("("+ str(vote)+","+ str(index) + ",'" + like + "')")
>         votec = 1
>     f.write(";\n")
>     nreports = random.randrange(2, 10)
>     reports = random.sample(range(1, nnames), nreports)
>     f.write("INSERT INTO Report (user_id, content_id)\nVALUES\n")
>     rep = 0
>     for report in reports:
>         if(author_id == report):
>             continue
>         if(rep != 0):
>             f.write(",\n")
>         rep = 1
>         f.write("("+ str(report)+","+ str(index) + ")")
>     f.write(";\n")
> for i in range(nanswers):
>     f.write("INSERT INTO Content (user_id, content ,date , edited)\nVALUES\n")
>     edited = "false"
>     if(random.random() < 0.5):
>         edited = "true"
>     date = int((50 - 1)*random.random() + 1)
>     question_id = int((nquestions - 1)*random.random() + 1)
>     author_id = int((nnames - 1)*random.random() + 1)
>     f.write("(" + str(author_id) + ",'" + fake.text()+ "'," "NOW() - INTERVAL '" + str(date) + " days'," +edited+ ");\n")
>     f.write("INSERT INTO Commentable (id)\nVALUES\n")
>     index = nquestions+i + 1
>     content_user[index] = author_id
>     f.write("("+ str(index) + ");\n")
>     f.write("INSERT INTO Answer (id, question_id)\nVALUES\n")
>     f.write("("+ str(index)+","+ str(question_id) + ");\n")
>     f.write("INSERT INTO Notification (user_id, date, viewed)\nVALUES\n")
>     viewed = "false"
>     if(random.random() < 0.2):
>         viewed = "true"
>     f.write("("+ str(content_user[question_id])+","+"NOW() - INTERVAL '" + str(date) + " days',"+ viewed+");\n")
> 
>     f.write("INSERT INTO AnswerNotification (notification_id, question_id, answer_id)\nVALUES\n")
>     f.write("("+ str(nnotification)+","+ str(question_id) + ","+ str(index) + ");\n")
>     nnotification += 1
> for i in range(ncomments):
>     f.write("INSERT INTO Content (user_id, content ,date , edited)\nVALUES\n")
>     edited = "false"
>     if(random.random() < 0.5):
>         edited = "true"
>     ncontent = nquestions+nanswers
>     f.write("(" + str(int((nnames - 1)*random.random() + 1)) + ",'" + fake.text()+ "'," "NOW() - INTERVAL '" + str(int((50 - 1)*random.random() + 1)) + " days'," +edited+ ");\n")
>     f.write("INSERT INTO Comment (id,commentable_id)\nVALUES\n")
>     index = nanswers+nquestions+i + 1
>     f.write("("+ str(index) + ","+str(int((ncontent - 1)*random.random() + 1)) +");\n")
>     f.write("INSERT INTO Notification (user_id, date, viewed)\nVALUES\n")
>     viewed = "false"
>     if(random.random() < 0.2):
>         viewed = "true"
>     f.write("("+ str(content_user[question_id])+","+"NOW() - INTERVAL '" + str(date) + " days',"+ viewed+");\n")
>     f.write("INSERT INTO CommentNotification (notification_id, comment_id)\nVALUES\n")
>     f.write("("+ str(nnotification)+","+str(index)+");\n")
>     nnotification += 1
> #for i in range(100):
> #    f.write(fake.text())
> #    f.write("\n")
> #f.close()
> 
> #open and read the file after the appending:
> ```

#### 7.2 Sitemap

> * There isn't a Terms of service and Privacy Policy page

#### 7.3 Additional Business Rules

> * Admin can also select the correct answer
> * Removed Rule: a user cannot report another user more than once.

#### 7.4 Database Workload

> * Changed Badge Order of Magnitude from 1k to 20 and Growth from 100 to 1
> * Changed Unblock Request Order of Magnitude from 10 to 100 and Growth from 1 to 10

### 8\. Implementation Details

#### 8.1. Libraries Used

| Name | Reference | Description of Use | Example |
|------|-----------|--------------------|---------|
| Laravel | [Reference](https://laravel.com/) | Laravel is utilized to expedite back-end development by establishing a secure and modular framework that implements standard website functionalities. | [Using laravel csrf to login](https://lbaw2357.lbaw.fe.up.pt/login) |
| Bootstrap | [Reference](https://getbootstrap.com/) | Employed to front-end development, it enhances aesthetics and responsiveness, elevating the visual appeal and usability of designs. | [User Profile Settings](https://lbaw2357.lbaw.fe.up.pt/editprofile/13) |
| MailTrap | [Reference](https://mailtrap.io/) | MailTrap is utilized to send emails between the "server" and the end user. In this case we implemented it to send the forgot password emails, so that the user can change it. | [Forgot Password](https://lbaw2357.lbaw.fe.up.pt/forgot) |

_Table 73: Libraries Used_

#### 8.2 User Stories

> This subsection should include all high and medium priority user stories, sorted by order of implementation. Implementation should be sequential according to the order identified below.
>
> If there are new user stories, also include them in this table. The owner of the user story should have the name in **bold**. This table should be updated when a user story is completed and another one started.

| US Identifier | Name | Module | Priority | Team Members | State |
|---------------|------|--------|----------|--------------|-------|
| US01 | View Top Questions | M01 | High | **Tomás** | 100% |
| US02 | View Recent Questions | M01 | High | **Tomás** | 100% |
| US03 | Browse Questions | High | M01 | **Tomás** Diogo | 100% |
| US04 | Browse Questions by Tags | M01 | Medium | **Tomás** Diogo | 100% |
| US05 | View Question Details | M01 | Medium | **Tomás** | 100% |
| US06 | View User Profiles | M01 | Medium | **Rodrigo** Tomás Diogo | 100% |
| US07 | Log-in the System | M02 | High | **Rodrigo** | 100% |
| US08 | Register in the System | M02 | High | **Rodrigo** | 100% |
| US09 | Logout | M03 | High | **Rodrigo** | 100% |
| US10 | View Profile | M03 | High | **Rodrigo** Diogo | 100% |
| US12 | Edit Profile | M03 | High | **Rodrigo** | 100% |
| US13 | Search Exact Match | M03 | High | **Tomás** | 100% |
| US14 | Search Full-Text | M03 | Low | **Tomás** | 100% |
| US15 | View Personal Feed | M03 | High | **Tomás** Diogo | 100% |
| US16 | Post Question | M03 | High | **Tomás** | 100% |
| US17 | Post Answer | M03 | High | **Tomás** | 100% |
| US18 | View My Questions | M03 | High | **Tomás** Rodrigo | 100% |
| US19 | View My Answers | M03 | High | **Tomás** Rodrigo | 100% |
| US20 | Recover Password | M03 | Medium | **Rodrigo** | 100% |
| US21 | Delete Account | M03 | Medium | **Rodrigo** | 100% |
| US22 | Support Profile Picture | M03 | Low | **Rodrigo** | 100% |
| US23 | View Personal Notifications | M03 | Medium | **Tomás** | 100% |
| US24 | Search over Multiple Attributes | M03 | Low | **Tomás** | 100% |
| US25 | Search Filters | M03 | Medium | **Tomás** | 100% |
| US26 | Vote on Questions | M03 | Medium | **Diogo** | 100% |
| US27 | Vote on Answers | M03 | Medium | **Diogo** | 100% |
| US28 | Comment on Questions | M03 | Medium | **Tomás** | 100% |
| US29 | Comment on Answers | M03 | Medium | **Tomás** | 100% |
| US30 | Follow Question | M03 | Medium | **Rodrigo** | 100% |
| US31 | Follow Tags | M03 | Medium | **Tomás** | 100% |
| US32 | Placeholders in Form Inputs | M03 | Medium | **Diogo** Rodrigo | 100% |
| US33 | Contextual Error Messages | M03 | Medium | **Rodrigo** Tomás Diogo | 100% |
| US34 | Contextual Help | M03 | Low | **Rodrigo** Tomás Diogo | 100% |
| US35 | About US | M03 | Low | **Rodrigo** Diogo | 100% |
| US36 | Main Features | M03 | Low | **Diogo** Rodrigo | 100% |
| US37 | Contacts | M03 | Medium | **Diogo** Rodrigo | 100% |
| US38 | Appeal for Unblock | M03 | Low | **Rodrigo** | 100% |
| US39 | Ordering of the results | M03 | Low | **Tomás** Rodrigo | 100% |
| US40 | Report Content | M03 | Low | **Rodrigo** | 100% |
| US41 | Support User Badges | M03 | Low | **Rodrigo** | 100% |
| US42 | Donations | M03 | Low | **Diogo** | 80% |
| US43 | User number of Answers | M03 | Low | **Rodrigo** | 100% |
| US44 | User number of Questions | M03 | Low | **Rodrigo** | 100% |
| US44 | User Points | M03 | Low | **Rodrigo** | 100% |
| US45 | Edit Question | M04 | High | **Tomás** | 100% |
| US46 | Delete Question | M04 | High | **Tomás** | 100% |
| US47 | Edit Answer | M04 | High | **Tomás** | 100% |
| US48 | Delete Answer | M04 | High | **Tomás** | 100% |
| US49 | Delete Comment | M04 | Medium | **Tomás** | 100% |
| US50 | Edit Question Tags | M04 | Medium | **Tomás** | 100% |
| US51 | Mark Answer as Correct | M04 | Medium | **Diogo** | 100% |
| US51 | Delete Content | M05 | Medium | **Tomás** Rodrigo | 100% |
| US52 | Edit Question Tags | M05 | Medium | **Tomás** | 100% |
| US53 | Manage Content Reports | M05 | Low | **Rodrigo** | 100% |
| US54 | Administer User Accounts (search, view, edit, create) | M06 | High | **Rodrigo** | 100% |
| US55 | Manage Tags | M06 | Medium | **Tomás** Diogo Rodrigo | 100% |
| US56 | Administrator Accounts | M06 | Medium | **Rodrigo** | 100% |
| US57 | Block and Unblock User Accounts | M06 | Medium | **Rodrigo** | 100% |
| US58 | Delete User Account | M06 | Medium | **Rodrigo** | 100% |

_Table 74: Implemented User Stories_

## A10: Presentation

### 1\. Product presentation

> Introducing QthenA—an innovative web-based information system revolutionizing the Q&A landscape. Our platform is meticulously designed to empower a vibrant community of users to effortlessly post questions, share thoughts through answers, and engage in dynamic discussions. With a simple yet amazing interface and robust functionalities such as voting, commenting, and personalized scoring, QthenA fosters a collaborative environment where knowledge is shared, curated, and valued. Supported by vigilant administrators and moderators, we ensure a seamless user experience while upholding content integrity and community standards.

> At its core, QthenA is driven by the commitment to redefine the Q&A experience. With user focused features like customizable profiles and advanced search capabilities, our platform transcends the traditional question-and-answer format. QthenA is a platform grounded with HTML5, CSS, JavaScript. It also uses the Laravel framework and Bootstrap framework.

> URL to the product: https://lbaw2357.lbaw.fe.up.pt/login

### 2\. Video presentation
>
>![video print]![image](uploads/c952eebb57510e9f4f02bdf5fcdba091/image.png)
>
> [Video presentation](https://git.fe.up.pt./lbaw/lbaw2324/lbaw2357/-/blob/main/reports/lbaw2357.mp4?ref_type=heads)




## Revision history

Changes made to the first submission:
1. ...

***

Checklist: https://docs.google.com/spreadsheets/d/1vSBmSlKg5PRKLTYRqQey_n-NZASlNTnItmUiX50-mJU/edit?usp=sharing

---

---

GROUP2357, 21/12/2023

* Group member 1 Diogo Sarmento, up202109663@fe.up.pt (editor of A9/A10)
* Group member 2 Rodrigo Povoa , up202108890@fe.up.pt (editor of A9/A10)
* Group member 3 Tomás Sarmento, up202108778@fe.up.pt (editor of A9/A10