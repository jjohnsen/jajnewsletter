CREATE TABLE jajdelivery (
    id int(11) NOT NULL auto_increment,    
    newsletter_issue_id int(11) DEFAULT 0 NOT NULL,
    subscription_user_id int(11) DEFAULT 0 NOT NULL,
    status tinyint(1) DEFAULT 0 NOT NULL,
    tries tinyint(1) DEFAULT 0 NOT NULL,
    tstamp int(11) DEFAULT 0 NOT NULL,
    PRIMARY KEY (id),
    UNIQUE (newsletter_issue_id, subscription_user_id),
    KEY jajdelivery_newsletter_issue_id (newsletter_issue_id),
    KEY jajdelivery_subscription_user_id (subscription_user_id),
    KEY jajdelivery_status (status)
);

ALTER TABLE ezsurvey TYPE = innodb;
