
# AldafluxUserRoleTypeBundle"


## Requirements

| Package       | Version          |
| ------------- | ---------------- |
| PHP           | ^7.1             |
| Symfony       | ^4.0, ~5.0 |

## Installation

### Step 1 : Download the bundle

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle :

```sh
composer require aldaflux/user-role-type-bundle:dev-master
```
    
This command is used if composer is installed in your system.

 
    
## Usage

### Form type

```php
use  Aldaflux\AldafluxUserRoleTypeBundle\Form\Type\UserRoleType;
```
    
    
```php
$builder->add("roles", UserRoleType::class); // use default configuration
$builder->add("roles", UserRoleType::class, ['config'=>"myconfigsuper"]); // use personnal configuration
$builder->add("roles", UserRoleType::class, ['config'=>"myconfigsuper", 'profile'=>"default"]); // use personnal configuration but overide the profiles
```
    

## CONFIG

* **display [all|standard|'minimum']**
	* **all**: Display all the roles avaibles, disabled some if **security** :strict
	* **standard**: Display all the roles the current user is granted,  disabled some if **security** :strict
	* **minimum**: Display all the roles the current user and the edited user are granted,  disabled some if **security** :strict

The roles availbles are all the role in the hierarchy, unless a profile is configured in the config or the builder

* **display [all|standard|minimum]**
	* **all**: Display all the roles avaibles, disabled some if **security** :strict
	* **standard**: Display all the roles the current user and the edited user are granted,  disabled some if **security** :strict
	* **minimum**: Display all the roles the current user and the edited user are granted,  not diplaying the other

* label
	* 	**display : [asItIs|word|traduction]**
		* asItIs : ROLE_USER, ROLE_ADMIN, ROLE_SUPER_ADMIN
		* word : User, Admin, Super Admin
		* traduction : user.roles.role_user, user.roles.role_admin, user.roles.role_super_admin

if traduction is activate, you can use **messages+intl-icu.en**
```yaml
user:
    roles:
        role_user: A standard user
        role_admin: Adminstrator of the site
```        


 ### aldaflux_user_role_type.yaml exemple

The type can work whitout this file, but for specific configs / profiles 
 
```yaml
    aldaflux_user_role_type:
	    configs:
	        default:
	            display: standard #by default , optionnal [all|standard|minimum]
	            security_checked: true #by default, optionnal
	        myconfigsuper:
	            display: all
	            profile: myprofilesuper # if not set : all the roles in hierarchy
	            security_checked: false # the user can grant whith role he hasn't... dont do that
                myconfigspecific:
	            display: minimum
	    profiles:
	        myprofile: [ROLE_ADMIN, ROLE_USER, ROLE_EDITOR]
	        myprofilesuper: [ROLE_SUPER_ADMIN,ROLE_ADMIN, ROLE_USER, ROLE_EDITOR]
	    label:
	        display: traduction #by default, optionnal, [asItIs|word|traduction]
	        translation_prefixe: "user.roles." #by default, optionnal, used if display:traduction
```

