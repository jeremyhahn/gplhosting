<HTML>
<HEAD>
<TITLE>Project GPL Hosting</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link type="text/css" rel="stylesheet" href="style.css">
<style type="text/css">
<!--
.style2 {font-size: 10px}
-->
</style>
</HEAD>
<BODY BGCOLOR=#FFFFFF leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" link="#666699">
<table width=780 border=0 cellpadding=0 cellspacing=0 height="383" bgcolor="#FFFFFF">
<?php include("header.html"); ?>
  <tr> 
    <td colspan=3 background="images/links.gif"> 
     <?php include("navigation.html"); ?>
    </td>
  </tr>
  <tr> 
    <td colspan=3 height="233"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="10" height="188">
        <tr> 
          <td height="212"><p align="center" class="BodyHeader">&nbsp;</p>
            <table width="749" border="0" align="center">
              <tr>
                <td width="538"><div align="center"><span class="BodyHeader">Help :: Dynamic DNS Client For the Windows Platform </span></div></td>
                <td width="201" rowspan="2"><span class="BodyHeader"><span class="menu"><img src="images/Help/intro.gif" width="200" height="150"></span></span></td>
              </tr>
              <tr>
                <td><p class="menu">The dynamic DNS client for the window platform installs as an NT service and runs on your server or workstation to constantly keep your DNS A-Records up to date in the event of an IP change. </p>
                <p class="menu">&nbsp;</p></td>
              </tr>
            </table>
            <p class="menu"><span class="BodyHeader">Introduction To Dynamic DNS</span><br>
            The internet uses IP addresses to direct traffic to its proper destination. IP addresses are just like your mailing address. When the mailman comes down the street to deliver your mail, he does not know you by your name. Instead, he knows you by your address. Your name on the letter is just an <I>alias</I> per say. The internet works in much the same way. Everything on the internet uses IP addresses to <I>route </I>or direct frames/packets which contain data, across the internet to its destination. Data is stored in what is known as a packet, and then the packet is inserted into what is called a frame. The frame stores all of the information which routers along the way use, to determine where to send the frame next. The frame is made up of many parts, two being the source address and the destination address. As you might have guessed, the source and destination addresses are IP addresses which tell the routers where the information came from <I>(so that it knows who to respond to)</I> as well as where its final destination point is located.</p>
            <p class="menu">The problem with routing on the internet, is that it is very hard to remember multiple IP addresses. Especially today with the many web sites that we visit, trying to remember all of their IP addresses would be a nightmare. Thanks to DNS, we can use friendly names like www.mydomain.com, which are <I>aliases</I> for an IP address. So, now instead of saying, 'Hey, Joe! Check out my new website! The address is http://123.123.121.231/joe ', you would simply say 'Hey, Joe! Check out my new website, its www.checkitout.com.</p>
          <p class="menu"> Now that we understand what DNS does, lets look at what dynamic DNS is exactly. When you are trying to run a server, you need an address that will always be the same. For instance, if Jim sends Steve a letter, and Steve moves before he gets the letter, without the help of mail forwarding<I> (which we also offer!)</I>, he would never get&nbsp; the letter. The internet uses this same type of principal. If your internet connection uses a dynamic IP address<I> (one which periodically changes),</I>&nbsp; without the help of dynamic DNS, it is just about impossible to run a server. In much the same way that Steve would never have gotten his letter, a request will never reach your server, unless you manually make the update in DNS. Thanks to dynamic DNS, these updates are performed automatically, often within minutes of the IP change. The dynamic update client monitors your IP address, and will automatically update your domain's A-Record every time an IP change is detected. </p>
          <p align="center" class="menu">&nbsp;</p>
          <p align="center" class="menu">&nbsp;</p>
          <p align="left" class="menu"><span class="BodyHeader">Command Line Arguments</span><br>
The update client also accepts command line arguments, which allow
administrators to develop batch files, applications, etc,. to control the 
dynamic update client. The update client supports the following command 
line parameters.
          <p align="left" class="menu">/install - Installs the update client as an NT service.<br>/uninstall - Uninstall's the update client as an NT service.<br>/start - Starts the dynamic update client.
<br>/stop - Stops the dynamic update client.
          <br>/DoDynamicUpdate - This option will force the client to check your current
          IP address, updating any zones if necessary. <br>
          <p align="left" class="menu">NOTE: The command line parameters are CaSe SeNsItIvE. The /DoDynamicUpdate will be accepted in ALL LOWERCASE. Other than this
              exception, these commands must be executed just as you see them. <br>
              <br>
              <br>
              <br>
              <br>
            <span class="BodyHeader">The Database</span> <br>The dynamic update client uses a Microsoft Access database to store all
    dynamic DNS and Failover domains, as well as global configurations. Since 
    the dynamic update client uses a backend database, it allows developers to 
    design custom applications which can manipulate each tables contents, from 
    either a local or remote environment. Another great feature of the backend 
    database, is the ability to create queries and reports which can be used 
    for historical or statistical archives. Since all log files are stored in 
    the database on a per zone basis, it allows for very precise tracking 
    ability, which can be used to further generate useful archives, which can 
    be used to fine tune your configurations in an attempt to achieve optimum 
    performance from your network. <br>
    <br>
    <br>
    <span class="BodyHeader"><br>
    Remote Administration Capabilities</span><br>
    The remote administration interface takes advantage of the backend 
    database to deliver advanced options to developers. Any savvy developer 
    can build an interface which will remotely connect to the database, and
    manipulate the data which the dynamic update client is responsible for
    maintaining. We have offered a very basic, highly customizable web 
    interface as a download, in an attempt to demonstrate the possibilities 
    which are available through remote administration. <br>
    (This sample code is provided 'AS IS' with no warranties of any kind. The 
    sample code is not recommended for use in a commercial environment, as it
    has been provided as a sample script ONLY! If you would like us to build 
    you a custom interface to taylor fit your needs, please contact us for an 
    immediate quote.) <br>
    <br>
    <br>
    <br>
    <span class="BodyHeader">Proxy Support</span><br>
    The client has built in proxy server detection. At the current time, we do 
    not have support for SOCKS proxy server configurations from the options 
    tab in the dynamic update client. If you are using a SOCKS proxy server,
    you must manually configure the settings using the proxycfg.exe utility, 
    which was placed in the %windir%\system32 directory when the update client 
    was installed on your system.
    THE FIRST THING YOU NEED TO CHECK is your Internet Explorer proxy
    settings. This can be achieve by navigating to the CONTROL PANEL &gt;
    INTERNET OPTIONS. When you are looking at the general tab for Internet
    Explorer properties, click on the CONNECTIONS tab. You will see a LAN
    SETTINGS button on the bottom of the page, which will open the Internet
    Explorer proxy settings when you click on it. In this window, you will see
    the very first check box, that says AUTOMATICALLY DETECT SETTINGS. This
    check box MUST be checked off if you wish to take advantage of the
    automatic proxy configuration abilities. If you are behind a proxy server
    and the update client is not detecting your settings, please click on 
    TOOLS &gt; OPTIONS from the menu bar on the dynamic update client. Once you
    are in the options menu, you may manually configure the proxy settings by
    clicking on the PROXY tab. If you need to reset the proxy configuration on
    the client, you may click on the reset button, which will configure your 
    WinHTTP settings to use a direct connection to the internet (No proxy). 
    (A detailed description about the proxycfg.exe utility can be read about 
    on Microsoft's MSDN website <a href="http://msdn.microsoft.com/library/default.asp?url=/library/en-us/xmlsdk/html/ServerXMLHttpProxy.asp">here</a>.)
          <p align="left" class="menu">
          <p align="left" class="menu">          
          <p align="left" class="BodyHeader">Configuring the Dynamic Update Client 
          <p align="center" class="menu">The first screen which requires user input is the authentication form. Enter the username / password pair that you use to access your account at GPL Hosting.</p>
          <p align="center" class="menu">&nbsp;</p>
          <p align="center" class="menu"><img src="images/Help/auth.gif" width="200" height="150"></p>
          <p align="center" class="menu">&nbsp;</p>
          <p align="center" class="menu">The next screen prompts for your hostname and domain. </p>
          <p align="center" class="menu">&nbsp;</p>
          <p align="center" class="menu"><img src="images/Help/domain.gif" width="200" height="150"></p>
          <p align="center" class="menu">&nbsp;</p>
          <p align="center" class="menu">The hostname is the name to the left of your domain, for instance, hostname.domain.com. Even if you are using a third level domain, the hostname will be the name left of your domain, for instance, hostname.third.level.com. A more practical example for a top level domain would be:</p>
          <p align="center" class="menu">mail.gplhosting.org</p>
          <p align="center" class="menu">mail = hostname, gplhosting.org = domain</p>
          <p align="center" class="menu">or for a third level domain:</p>
          <p align="center" class="menu">mail.user01.gplhosting.org</p>
          <p align="center" class="menu">mail = hostname, user01.gplhosting.org = domain </p>
          <p align="center" class="menu">If you are not using a hostname, you can just use the '@' symbol to signify the 'root' domain. </p>
          <p align="center" class="menu">&nbsp;</p>
          <p align="center" class="menu">After your   domain configuration is complete, the dynamic update client will prompt you to finish the setup process, where the update client will perform the first attempt at a dynamic update. Inside the main window, you can check the status of your domain, as well as add/remove zones from your dynamic update list.</p>
          <p align="center" class="menu">&nbsp;</p>
          <p align="center" class="menu">For your archiving and statistical uses, you may right click on the main window, and choose View Details, where you will be taken to your account's dynamic DNS log files located at the GPL Hosting server.</p>
          <p align="center" class="menu">&nbsp;</p>
                    <table width="50%" border="0" align="center" class="menu">
            <tr>
              <td width="53%"><div align="center">
                  <p><a href="HELP_DNS.php">Return To Main DNS Menu</a></p>
              </div></td>
              <td width="47%"><div align="center"><a href="HELP.php">Return To Main Help Menu</a></div></td>
            </tr>
          </table>
                    <p>&nbsp;</p></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td colspan=3 height="14"> 
      <div align="center"> 
        <table width="100%" border="0" cellspacing="0" cellpadding="0" height="35" align="center">
          <tr> 
            <td background="images/index_08.gif" height="35"> 
              <?php include("footer.html"); ?>
            </td>
          </tr>
        </table>
      </div>
    </td>
  </tr>
</table>
</BODY>
</HTML>