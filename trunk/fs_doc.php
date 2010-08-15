<div class="docu">
<h1><?php _e('Fotoslide'); ?></h1>
<p><?php _e('FotoSlide provides a simple way to present your photos in a gallery using your existing WordPress Media Library. It is a project that was developed as a branch of <a href="http://code.google.com/p/wps3slider/">WPS3Slider</a>. Through all the feedback that was received from this plugin, I decided to re-write the plugin from the ground up, starting with a new <a href="http://code.google.com/p/intelislide/">jQuery plugin</a> that was more flexible than the s3Slider used in WPS3Slider. The result is a more robust WordPress plugin that integrates with WP seamlessley.')?></p>

<h2><?php _e('Creating Galleries'); ?></h2>
<p><?php _e('In the <strong>Media</strong> tab, click on the \'FotoSlide\' option')?></p>
<p><img src="<?php echo WP_PLUGIN_URL; ?>/fotoslide/assets/screenshot-1.png" alt="<?php _e('Select FotoSlide from Media Menu'); ?>" /></p>
<p><?php _e('Create a new gallery by clicking the new gallery button and entering information about the gallery. The options available are;')?></p>
<ul>
  <li><?php _e('<strong>Gallery Name:</strong> Provide a name to identify it.')?></li>
  <li><?php _e('<strong>Width:</strong> The gallery width.')?></li>
  <li><?php _e('<strong>Height:</strong> The gallery height.')?></li>
  <li><?php _e('<strong>Timeout:</strong> The length of time an image displays before fading out in miliseconds e.g. 1000 = 1 second.')?></li>
  <li><?php _e('<strong>Transition Speed:</strong> The length of time it takes to fade out an image.')?></li>
</ul>
<h2><?php _e('Adding images to a gallery'); ?></h2>
<p><?php _e('Select the gallery you want to add images to by clicking on the pictures icon in the corresponding table row.')?></p>
<p><img src="<?php echo WP_PLUGIN_URL; ?>/fotoslide/assets/screenshot-2.png" alt="<?php _e('Select pictures icon'); ?>" /></p>
<p><?php _e('Now click on the <strong>Add an image to this gallery</strong> button to show the image add form. Clickin on the <strong>Select Image from Media Library</strong> button will launch your media livrary in a thickbox window. Select the <strong>Insert</strong> button next to the image you want to add and then edit the image properties;')?></p>
<ul>
  <li><?php _e('<strong>Image Link:</strong> (optional) You can provide a link to a location of your choosing.')?></li>
  <li><?php _e('<strong>Caption Message:</strong> (optional) A caption for the image.')?></li>
  <li><?php _e('<strong>Caption Location:</strong> (optional) Where the caption will appear over the image.')?></li>
  <li><?php _e('<strong>Caption Opacity:</strong> (optional) The opacity of the caption.')?></li>
  <li><?php _e('<strong>Colours:</strong> (optional) Specify colour values for caption background colour and text colour')?></li>
  <li><?php _e('<strong>Order:</strong> The order of the image')?></li>
</ul>
<h2><?php _e('Adding galleries to your pages/posts')?></h2>
<p><?php _e('In the post editor, click on the galleries icon in the media menu.')?></p>
<p><img src="<?php echo WP_PLUGIN_URL; ?>/fotoslide/assets/screenshot-3.png" alt="<?php _e('Select pictures icon'); ?>" /></p>
<p><?php _e('From the thickbox window, select the code from the image gallery you want and paste it  in the editor box')?></p>
<p><?php _e('<strong>Tip: </strong>You can add multiple galleries on the same page.')?></p>
<h2><?php _e('Adding galleries to theme files'); ?></h2>
<p><?php _e('This plugin also supports short tags so you can also execute the short code by adding...')?></p>
<code>
do_shortcode('[fs id="{ID}"'); // where {ID} is the gallery id
</code>
<h2><?php _e('Providing additional CSS to galleries'); ?></h2>
<p><?php _e('Each gallery renders with an encapsulating span element with its id value in relation to the gallery id')?></p>
<code>
&lt;span id="fotoslide-{ID}" class="fotoslide"&gt;
<br /><br />
// code
<br /><br />
&lt;span&gt;
</code>
<p><?php _e('So you can target individual galleries with the id attribute or provide styles across all galleries with the \'fotoslide\' class')?></p>
<h2><?php _e('Issues and bugs'); ?></h2>
<p><?php _e('This project is being managed on google code so if you come across any issues or have a feature request, please direct them to <a href="http://code.google.com/p/fotoslide/issues/list">http://code.google.com/p/fotoslide/issues/</a>')?></p>
</div>