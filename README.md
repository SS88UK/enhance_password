# Enhance Email Password plugin for RoundCube

Enable the ability for RoundCube users to change their email password for the Enhance Control Panel


## Demo Visual
![Demo SS](https://i.ibb.co/Pr3VbJV/Screenshot-2023-11-29-at-9-09-47-AM.png)

## Deployment (Centrally Installed Roundcube)

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

## Deployment (Customer's Domains i.e. mail.customer.com)

Note: the user for this deployment is `roundcubelocal`, so you would need to `su - roundcubelocal` to gain access.

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

Edit your `/path/to/roundcube/config/config.inc.php` file and add `orchd_key` (API key) and `orchd_url` (control panel API endpoint) to the `$config` variable. You can find these details in the `config.inc.php` file of the centrally installed Roundcube or you can generate a new key in Settings -> Access Tokens in your cntrol panel. The end result should look something like the following: 

```
$config['orchd_key'] = 'XXXXXXXXXXXXX';
$config['orchd_url'] = 'https://control-panel-url.com/api';
```
