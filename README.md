## Integrations
This is an app to host all your custom integrations

#### Installation
- Setup the app locally on your laptop/desktop
- Push your changes using rsync/sftp to the server

Download or git clone or the repository into a sub-folder locally on your laptop
```bash
$ git clone git@bitbucket.org:startupbros/integrations.git integrations
```

Use [Composer](https://getcomposer.org/) to install dependencies
```bash
$ composer install
```

Create .env from .env.example
```bash
$ cp app/.env.example app/.env
```

Make sure to set these properties to desired values in app/.env
```bash
# integration report will be sent to this email
MAIL_TO_ADDRESS='example@example.com'

# woopra project id
WOOPRA_PROJECT=''
```
**Congratulations**. You are set to develop the app locally. Happy coding.

Push your changes to the server using rsync
```
# dry-run/verify the list of files/folder that need to be pushed
rsync --exclude '.env' --exclude '.git' --exclude 'logs' -anv ./ startupbros@startupbros.ssh.wpengine.net:~/sites/startupbros/integrations/

# push the changes to server
rsync --exclude '.env' --exclude '.git' --exclude 'logs' -anv ./ startupbros@startupbros.ssh.wpengine.net:~/sites/startupbros/integrations/
```

#### TODO
- ~~write improvely integration to show the app can be used for multiple integrations~~
- ~~find better dependency injection as slim DI is restrictive~~
- find a better way instead of using $_session to store temp data
