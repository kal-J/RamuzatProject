<style type="text/css">
  body.unsupported {
  background: #f2f2f3;
  width: 100%;
}   
.unsupported .container {
  width: 100%;
  min-width: 300px;
}
.unsupported-browser {
  background: #fff;
  margin: 35px auto;
  width: 100%;
  box-shadow: 0 0 5px #cacace;
  position: relative;
  padding: 20px 30px;
  text-align: left;
}
.unsupported-browser h1 {
  font-size: 2em;
  font-weight: 0;
}
.unsupported-browser h2 {
  border: 0;
}
.unsupported-browser li {
  margin-bottom: 5px;
}
.unsupported-browser li a {
  color: inherit;
}
.unsupported-browser li a:hover {
  color: #007bc3;
}
.unsupported hr{
  margin-top: 50px;
}
.unsupported-message {
  font-size: 0.9em;
  margin-top: 40px;
  margin-bottom: 30px;
}
.unsupported-message li {
  margin-bottom: 5px;
}
</style>
<html>
   <link href="<?php echo base_url("myassets/css/bootstrap.min.css"); ?>" rel="stylesheet">
  <body class="unsupported">
    <div class="grid-tools FullSite">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
                  <div class="container-wrapper">
          <div class="container-full-column">
            <div class="unsupported-browser">
              <h1 class="text-warning"><center> <?php echo $this->agent->browser()." ".$this->agent->version(); ?> is not supported</center></h1>
              <h1 class="text-primary">Please upgrade your browser in order to use e-financi</h1>
              <p>We have built e-financi with the latest technology which improves the look of e-financi and gives you a better experience with new features and functions. Unfortunately, your browser does not support these technologies.</p>
              <br>
              <h5 class="text-danger">Please download one of these free and up-to-date browsers: </h5>
              <br>

              <ul>
                <li><a href="https://www.google.com/chrome/browser/desktop/" target="_blank">Chrome version 80+</a></li>
                <li><a href="https://www.mozilla.com/firefox/" target="_blank">Firefox version 75+</a></li>
                <li><a href="https://www.microsoft.com/en-us/windows/microsoft-edge" target="_blank">Microsoft Edge version 80+</a></li>
                
              </ul>
              <hr>
              <div class="unsupported-message">
              <h3>I can't update my browser</h3>
              <ul>
                <li>Ask your system administrator to update your browser if you cannot install updates yourself.</li>
                <li>If you can't change your browser because of compatibility issues, think about installing a second browser for utilization of this site and keep the old one for compatibility.</li>
              </ul>
              </div>
              <h3><center><a href="<?php echo base_url();?>">Try Again</a><h3>
          </div>
        </div>
          </div>
        </div>
      </div>
    </div>
  </body>