<?php
  /**
   * SAMPLE Code to demonstrate how provide SAML settings.
   *
   * The settings are contained within a SamlSettings object. You need to
   * provide, at a minimum, the following things:
   *  - idp_sso_target_url: This is the URL to forward to for auth requests.
   *    It will be provided by your IdP.
   *  - x509certificate: This is a certificate required to authenticate your
   *    request. This certificate should be provided by your IdP.
   *  - assertion_consumer_service_url: The URL that the IdP should redirect
   *    to once the authorization is complete. You must provide this, and it
   *    should point to the consume.php script or its equivalent.
   */

  /**
   * Return a SamlSettings object with user settings.
   */
  function saml_get_settings() {
    // This function should be modified to return the SAML settings for the current user

    $settings = new SamlSettings();

    // When using Service Provider Initiated SSO (starting at index.php), this URL asks the IdP to authenticate the user.
    $settings->idp_sso_target_url             = "https://app.onelogin.com/saml/signon/6171";

    // The certificate for the users account in the IdP
    $settings->x509certificate                = <<<ENDCERTIFICATE
-----BEGIN CERTIFICATE-----
MIIDSzCCAjOgAwIBAgIJAKeRY/5t5tc0MA0GCSqGSIb3DQEBCwUAMDwxEzARBgNV
BAMMCkl2YW4gTHVjY2kxJTAjBgkqhkiG9w0BCQEWFmdhbGtpbWFzZXJhOUBnbWFp
bC5jb20wHhcNMTUwMTE0MTExMTI1WhcNMjUwMTExMTExMTI1WjA8MRMwEQYDVQQD
DApJdmFuIEx1Y2NpMSUwIwYJKoZIhvcNAQkBFhZnYWxraW1hc2VyYTlAZ21haWwu
Y29tMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxDY3ozGA3mNYM1eR
1GeffChdlqkUYbEmOSk+xZQ2yf6Kx7ugxMqWq2nf7I6G93EwWLs+8I94+wqqCcIp
92nrzDyIRp5jtd0Sk8AWc5hME1aaDks8usVye3ELBn8uZrBQ9HMjn4I2p1o8ghvM
KG65UKFqarNlGtl7YNTi+uBJYRUE7o88ci0bs18fluTwuwVWky2nKwMTcA/fOiGV
7avd1Qn+VZD9tHejHzhbSwKdqAQNelT/khNc8POYb4o+4lQf+6gnWfgT/qITRKAi
s9nxAhNz/dNq6dXIJFa9ZPc8iWBbRMx/UERAZGwieODBNGrFcYYQGVyvbQbz0ljd
H4tJDQIDAQABo1AwTjAdBgNVHQ4EFgQUpeU3w905ziItrmXX20ky6s89EMQwHwYD
VR0jBBgwFoAUpeU3w905ziItrmXX20ky6s89EMQwDAYDVR0TBAUwAwEB/zANBgkq
hkiG9w0BAQsFAAOCAQEAC004mnEWkSCSLcwVF4fEqssOPF+SBpCnMhK7yxylP3m5
dgFmxiu0NJV1294xCm90L5JDZEPhOiUIfjdFWWaN9r4+t6KlYwKmYnlhGEn0q/RD
mWdz185Ez7YqAfM5lWgmJJJGYYLMo0t9lpOdAoY7OsgjzwWN8J/Lwo6e5IyYFwjH
49EzDK5ZRPf23Tm96lE2wtvRWwNlXfRPTxZSEu1YOrBkMAL7IVV2Dz4BaXPvQelS
P98CkRoQ3QAChESWYNcqGm2r1omsCdVvJNYikLhLY7fn+8/OOyRAZTIKGzLde3I6
BvvzpnR5a4xlQzsXaVtPz8Ta7kog9zeOoQum6nJcLg==
-----END CERTIFICATE-----
ENDCERTIFICATE;

    // The URL where to the SAML Response/SAML Assertion will be posted
    $settings->assertion_consumer_service_url = "http://localhost/php-saml/consume.php";

    // Name of this application
    $settings->issuer                         = "php-saml";

    // Tells the IdP to return the email address of the current user
    $settings->name_identifier_format         = "urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress";


    return $settings;
  }

?>
