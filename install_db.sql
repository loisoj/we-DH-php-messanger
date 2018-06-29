CREATE TABLE IF NOT EXISTS `User` (
  `UserId`       INT(11)      NOT NULL PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `UserName`     VARCHAR(50)  NOT NULL UNIQUE,
  `UserPassword` VARCHAR(100) NOT NULL,
  `UserPic`      VARCHAR(150) NOT NULL                      DEFAULT 'userpics/default.jpg'
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `Friends` (
  `FriendOneId` INT(11) NOT NULL,
  `FriendTwoId` INT(11) NOT NULL,
  FOREIGN KEY (`FriendOneId`) REFERENCES `User` (`UserId`),
  FOREIGN KEY (`FriendTwoId`) REFERENCES `User` (`UserId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `Message` (
  `MessageId`          INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `MessageAuthorId`    INT(11)             NOT NULL,
  `MessageRecipientId` INT(11)             NOT NULL,
  `MessageText`        TEXT                NOT NULL,
  `MessageDateTime`    DATETIME            NOT NULL,
  FOREIGN KEY (`MessageAuthorId`) REFERENCES `User` (`UserId`),
  FOREIGN KEY (`MessageRecipientId`) REFERENCES `User` (`UserId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `MessageAll` (
  `MessageId`       INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `MessageAuthorId` INT(11)             NOT NULL,
  `MessageText`     TEXT                NOT NULL,
  `MessageDateTime` DATETIME            NOT NULL,
  FOREIGN KEY (`MessageAuthorId`) REFERENCES `User` (`UserId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
