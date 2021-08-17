# P8 ToDo & Co - Authentication

Technical documentation explaining how authentication was implemented.

## Introduction

The application uses Symfony security mechanisms.

The two principles are authentication and authorization.

Since Symfony 5.3 which is used here, we use the Authenticator-based security system instead of the Guard Authenticator which is deprecated.

## Authentication

Users are represented by the User class which is a Doctrine entity. The unique element of this class is the name of the user,
this is why we find the annotation `@UniqueEntity (fields = {"username"})` at the level of this class.

The users are therefore stored in the "user" table of the SQL database.

In addition, the passwords are encrypted in the database, which is why in addition to implementing the UserInterface, this class also implements
the new PasswordAuthenticatedUserInterface to manage password getters and setters.

With the new version of this interface, released with version 5.3,
It is no longer necessary to use getSalt () and it is necessary to use getUserIdentifier () instead of getUsername ().

Symfony 5.3 deprecated the Guard Authenticator used so far. The application therefore uses the Authenticator-based.

This brings us to the security.yaml configuration file:

- So we have enabled `enable_authenticator_manager`.
- For the encryption of passwords, we do not use the "encode" but the new "password_hasher" and which we configure on "auto".
  As of Symfony 5.3, "auto" corresponds to bcrypt.


Still in the security.yaml file, we now enter the firewall configuration:
- We have two personalized services: the AccessDeniedHandler and LoginFormAuthenticator. The first manages connection errors (redirection and display of errors),
  the second is the authentication form. The'AccessDeniedHandler is first because it takes priority over the rest.
- Then we have the redirection route when disconnecting.
- Finally, the entry point for unauthenticated visitors which is the authentication form.


In the code, if we want to check if a visitor is connected we use the special attribute `IS_AUTHENTICATED_FULLY`.

To encrypt passwords, we use the new PasswordHasherInterface of Symfony 5.3.


## Authorization

Let's go back to the security.yaml file in the `access_control` section:

- Symfony 5.1 has deprecated the anonymous visitor. The new system no longer has anonymous authentication, sessions are simply unauthenticated.
  This is why in the access control we have `PUBLIC_ACCESS` routes which correspond to the routes authorized for unauthenticated visitors.

In the code, we use different means to control permissions:
- Annotation: @isGranted
- AbstractController :: denyAccessUnlessGranted
- Security :: isGranted which implements AuthorizationCheckerInterface

A central element is the management of roles. This is what sets the permissions. We attribute a role to a user,
and we give permissions to this role.

We have created two types of roles:
- ROLE_ADMIN
- ROLE_USER

Since the "anonymous" user no longer exists, we created an "anonymous" user and he was given the USER role.

In the access control we have defined the login and account creation pages for all visitors,
but the rest of the pages needs to be authenticated.

We have set up a Voting system to then define the permissions on different methods or routes
in the Task and User controllers.

## Documentation

- [Symfony Security](https://symfony.com/doc/current/security.html)
- [The Authenticator Manager](https://symfony.com/doc/current/security/authenticator_manager.html)
- [The Authentication System](https://symfony.com/doc/current/components/security/authentication.html)
- [Voters](https://symfony.com/doc/current/security/voters.html)
- [UniqueEntity](https://symfony.com/doc/current/reference/constraints/UniqueEntity.html)
- [Password Hasher](https://symfony.com/blog/new-in-symfony-5-3-passwordhasher-component)
- [Annotations](https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/security.html)