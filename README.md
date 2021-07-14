# Locals: example of simple API

## Introduction

This is an example of a simple API for Locals. I decided to build the platform from scratch rather than using a Framework. It seems easier for a simple thing like this, also will be faster and more extendible for large applications. The mini-framewrork I created, supports database cache, pre-validation, plaggins via composer and it is single-state, so it can scale horizontally in a multi-server scheeme.

## Arquitecture

### Filesystem

All the requests are centralized using the boostrap software pattern on the file index.php. Apache is setup so that only the folder /public can be seen, enhancing security, and .htaccess is used for clean URLs. The folder /app/ contains all the endpoints, each will be a class "Controller" with a public method "main()".

### Configs

Configurations are centralized on the folder /configs/ and can be acccessed globally on the framework using the class Config.

### Extensibility

The folder /classes/ contains global classes that are useful to interact with the data within the app. New classes can be created and organized into the workspace Locals\ for usage witin the app. The workspace is declared using Composer with the standar "psr-4". Composer is used to add new libraries easily to extend the framework.any non-compos

## Deployment

Clone the repo on the www folder of apache and create the virtualhost as stated on /configs. Rename the configuration file and add the database params and the JWT key. Clone the database and (optional) the test data on the /configs/ folder. Run the script ".deploy" to create tmp data and "composer install" to build dependancies. Restart apache, it should work!

## Enpoints

To access an endpoint, point your browser to:

www.yourdomain.com/endpoint/payload

Where "endpoint" will be the name of the one files on the folder /app/ (without the .php extension) and "payload" will be the base64 of a JSON structure like:

```
{"token":false, "username":"salvipascual", "password":"MySecureHash"}

```

Find the correct structure documented at the begining of each endpoint file.

Below an example of complete call. Please notice the base64 should be URL compatible, no "=" or "#" characteres should be presented.

www.yourdomain.com/login/eyJ0b2tlbiI6ZmFsc2UsICJ1c2VybmFtZSI6InNhbHZpcGFzY3VhbCIsICJwYXNzd29yZCI6Ik15U2VjdXJlSGFzaCJ9

To test all the available API endpoints without constructing the calls by yourseld, open /test.html and use the graphic interface. Make sure to obtain a token using "login" before calling entry points like "posts", which will require the auth token.

## Responses

On every petition, the response will be a JSON structure as follows:

```
{
    "error":false,
    "code":200,
    "message":"Session started correctly.",
    "payload":{
        "token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjIwNyIsInVzZXJuYW1lIjoic2FsdmlwYXNjdWFsIn0.4PEbeKC25589B9J5q9-mMyWMfDAPd5ys2puS3i8OKKQ"
    }
}
```

- "error" will be boolean if the system encaounter an error.
- "code" will be a return code, this is API based not HTTP codes.
- "message" will be a returning message that can be displayed to the user. Useful in case of errors.
- "payload" will be an structure with information to be used on the app. In instance, the above explample is a suscessfull /login call, and payload is the token to be kept on the app. If calling the /posts endpoint, the payload will be a JSON of the posts retrieved.

This structure will never change, except for the "payload", that is request specific.

## Changes to the database

I had to made the following changes in the database for the excersize. I am sorry if this is not what you had in mind and you expecting me to work the scheeme exactly as provided.

- Changed post.body to varchar to be able to perform searches
- Change all fields created_at and updated_at from int to timestamp
- Defaulted all fields created_at to current_timestamp()
- Add AUTO_INCREMENT to key field user.id
- Add unique index to user.username
- Change field user_mute.expired_at from int to timestamp

Please find the corrected database on the folder /congigs/, as well as test data to be used on the use cases.

## Oportunities of improvement

- Information is been sent directly from the database to the view. We should have a Model to handle the information properly
- A more proper description of the API (requests, responses and error codes) could have been made, but this is such a simple example that feels self-explanatory, so I prefer to deliver faster. Let me know if you want better documentation.
- It is returning plain text information; we can make the server return the content as JSON using the "return-type" header, but this is up to you.
- Validation of the content passed via the base64 token is not properly done and can make the system weak against XSS attacks.
- Long file and line of the runtime errors are been sent to the final user on the returning JSON. This was made this way for testing purposes, but should be fixed before production.
- I currently have a Windows Home and docker will not be installed on it. I can install docker on the server and create the box for you, I did not want to lose any more time delivering this code. If this is a skill you want to test, please LMK and I will create the docker box.

## Questions?

Shall you have any questions, please email or call me directly.
