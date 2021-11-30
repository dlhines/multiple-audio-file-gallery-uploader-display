<?php
$html = '
<!--- Multiple Audio File Gallery Uploader (MAFGUD)---!>
<div id="mafgud-render">
	<h5 class="mafgud-title">TITLE OF AUDIO FILE GALLERY</h5>
	<span>
		<h6 class="mafgud-song-title">AUDIO FILE TITLE</h6>
			<audio controls>
				<source src="SOURCE OF AUDIO FILE" type="audio/mpeg" alt="TITLE OF AUDIO FILE">
			<p>Your browser doesn\'t support HTML5 audio. Please check us our on our Social Media Accounts.</p>
		</audio>
	</span>
</div>
<!--- End (MAFGUD)---!>
';
?>
<style>
  .display_instructions .di_code{
    background: #FFF;
    color: #000;
    border: 1px solid #000;
    margin-left: auto;
    margin-right: auto;
    padding-left: 20px;
    padding-right: 20px;
    width: 70%;
  }
  .display_instructions hr {
    margin-top: 15px;
  }
</style>
<div class="admin-miud display_instructions clearfix">
  <h4>The shortcode is: </h4>
  <div class="di_code">
    <p>
      [mafgud_display id="THE PAGE ID" title="TITLE OF YOUR GALLERY"]
    </p>
  </div>
  <p>This shortcode is provided in the "Multiple Image Gallery Uploader Display (MIGUD)" configuration meta box for each Post Type you use with this plugin. It will automatically generate the id attribute for you. All you will have to do is supply a title (if needed), then copy paste the shortcode into that particular Post Type content window.</p>
  <hr />
  <h4>The Shortcode provided by this plugin produces the following html:</h4>
  <div class="di_code">
    <pre>
      <?php echo htmlentities($html); ?>
    </pre>
  </div>
  <hr />
  <h4>ID's and CLASS attributes:</h4>
  <h5>ID's</h5>
  <p>
    <b>mafgud-render</b>:<br />
    The only ID used to render output. It encloses all the generated <b>a</b> links, <b>span</b>, <b>h6</b>, <b>audio</b>, <b>source</b> tags.
  </p>
  <h5>CLASSes</h5>
  <p><b>mafgud-title</b><br />
    Style class for the optional TITLE OF AUDIO FILE GALLERY
    <div class="di_code"><p>#mafgud-render .mafgud-title</p></div>
  </p>
  <p><b>mafgud-song-title</b><br />
    Style class for the AUDIO FILE TITLE
    <div class="di_code"><p>#mafgud-render .mafgud-song-title</p></div>
  </p>
</div>
