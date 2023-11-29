# Enhance Email Password plugin for RoundCube

Enable the ability for RoundCube users to change their email password for the Enhance Control Panel


## Demo Visual
![Demo SS](https://i.ibb.co/Pr3VbJV/Screenshot-2023-11-29-at-9-09-47-AM.png)

## Deployment

Download a copy of this repo and upload the contents to:
```
/path/to/roundcube/plugins/enhance_password
```

Edit your `/path/to/roundcube/config/config.inc.php` file and add `enhance_password` to the `$config['plugins']` variable. It should look something like the following: 

```
$config['plugins'] = array(
    'enhance_login',
    'enhance_password'
);
```
