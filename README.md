educational-gaming-platform
===========================

COMMIT RULES

Your commits should be clear for teammates.
Mates need to search commits history to:
- Find source of a bug easier (bisection + quick check of commit messages history).
- Simplify source code reading - you found a strange place in the code,
  look at commit which introduced this code and understand why its there due to
  clear commit message.

So while you are commiting something to repository you need to follow some rules.
They are rather simple for now, because really strict but good rules slow down the development.
Here they are:
- Add subsystem tag before commit message header. It will allow to look for changes in particular
  subsystem easily.
  Example: "website: added orange fruit pic"
  Example: "api: introduced login/logout json commands server handler"
  Make your tags and use them if you don't see suitable tag in commit history.
- If you think that you've made a change that impacts project behavior significantly
  (can introduce a serious regression) add MAJOR tag to the beginning of your commit message.
  Example: "MAJOR js: rewritten client side js validation"
  Example: "db: Added field "location" to table "users""
- It is always better when each your commit has a single
  responsibility - changes a single thing. Try to do it if it doesn't need a lot of time.

==========================
UNIT TESTING

PHP is a dynamic language. Everything is checked in runtime so no help from ahead of time
compiler. But everytime we change code we need to be sure that almost nothing is broken.
That's why it is necessary to write unit tests for every feature you implement.
There are many other good things about unit tests.
PHP won the market but it is not a simple language to code correct programs. See this article:
http://me.veekun.com/blog/2012/04/09/php-a-fractal-of-bad-design/
This PHP weirdness forces us to code unit tests even more.

Once you don't code unit test for a new feature you make its state unpredictable during
further development cycle and you risk the project success. So please write as much tests
as feature exactly needs.

===========================
How to work with website:
1. Install netbeans IDE (free)
2. Install netbeans yii plugin: http://plugins.netbeans.org/plugin/47246/php-yii-framework-netbeans-phpcc
3. Open netbeans project educational-gaming-platform/website/protected/nbproject
4. Click "play" (run) button in the IDE toolbox. Website should just work out of the box.
   NetBeans project is set up already.
5. If NetBeans asks you about webserver, currently we use built in php webserver. We'll
	make a setup script for a more serious webserver later.

Useful info on setting up Netbeans project for yii: http://www.yiiframework.com/wiki/83/
Check it out to enable php debugging and other special stuff.

If something doesn't work contact eabatalov.
