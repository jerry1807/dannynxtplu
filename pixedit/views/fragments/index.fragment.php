<?php if (!defined('APP_VERSION')) die("Yo, what's up?"); ?>

<div id="post" class="skeleton skeleton--full" style="display:block;">
      
    <?php if (!$Settings->get("data.advanced")): ?>        
        <div class="col s12 m12 mt-0">
            <iframe iframe-set-dimensions-onload ng-src="<?= $Settings->get("data.endpoint") ?>"></iframe>
        </div>		      

    <?php else: ?>

       <div class="col s12 m12 mt-0">
            <pixie-editor>

            <div class="global-spinner">
                <style>.global-spinner {display: none; align-items: center; justify-content: center; z-index: 999; background: #fff; position: fixed; top: 0; left: 0; width: 100%; height: 100%;}</style>
                <style>.la-ball-spin-clockwise,.la-ball-spin-clockwise>div{position:relative;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}.la-ball-spin-clockwise{display:block;font-size:0;color:#1976d2}.la-ball-spin-clockwise.la-dark{color:#333}.la-ball-spin-clockwise>div{display:inline-block;float:none;background-color:currentColor;border:0 solid currentColor}.la-ball-spin-clockwise{width:32px;height:32px}.la-ball-spin-clockwise>div{position:absolute;top:50%;left:50%;width:8px;height:8px;margin-top:-4px;margin-left:-4px;border-radius:100%;-webkit-animation:ball-spin-clockwise 1s infinite ease-in-out;-moz-animation:ball-spin-clockwise 1s infinite ease-in-out;-o-animation:ball-spin-clockwise 1s infinite ease-in-out;animation:ball-spin-clockwise 1s infinite ease-in-out}.la-ball-spin-clockwise>div:nth-child(1){top:5%;left:50%;-webkit-animation-delay:-.875s;-moz-animation-delay:-.875s;-o-animation-delay:-.875s;animation-delay:-.875s}.la-ball-spin-clockwise>div:nth-child(2){top:18.1801948466%;left:81.8198051534%;-webkit-animation-delay:-.75s;-moz-animation-delay:-.75s;-o-animation-delay:-.75s;animation-delay:-.75s}.la-ball-spin-clockwise>div:nth-child(3){top:50%;left:95%;-webkit-animation-delay:-.625s;-moz-animation-delay:-.625s;-o-animation-delay:-.625s;animation-delay:-.625s}.la-ball-spin-clockwise>div:nth-child(4){top:81.8198051534%;left:81.8198051534%;-webkit-animation-delay:-.5s;-moz-animation-delay:-.5s;-o-animation-delay:-.5s;animation-delay:-.5s}.la-ball-spin-clockwise>div:nth-child(5){top:94.9999999966%;left:50.0000000005%;-webkit-animation-delay:-.375s;-moz-animation-delay:-.375s;-o-animation-delay:-.375s;animation-delay:-.375s}.la-ball-spin-clockwise>div:nth-child(6){top:81.8198046966%;left:18.1801949248%;-webkit-animation-delay:-.25s;-moz-animation-delay:-.25s;-o-animation-delay:-.25s;animation-delay:-.25s}.la-ball-spin-clockwise>div:nth-child(7){top:49.9999750815%;left:5.0000051215%;-webkit-animation-delay:-.125s;-moz-animation-delay:-.125s;-o-animation-delay:-.125s;animation-delay:-.125s}.la-ball-spin-clockwise>div:nth-child(8){top:18.179464974%;left:18.1803700518%;-webkit-animation-delay:0s;-moz-animation-delay:0s;-o-animation-delay:0s;animation-delay:0s}.la-ball-spin-clockwise.la-sm{width:16px;height:16px}.la-ball-spin-clockwise.la-sm>div{width:4px;height:4px;margin-top:-2px;margin-left:-2px}.la-ball-spin-clockwise.la-2x{width:64px;height:64px}.la-ball-spin-clockwise.la-2x>div{width:16px;height:16px;margin-top:-8px;margin-left:-8px}.la-ball-spin-clockwise.la-3x{width:96px;height:96px}.la-ball-spin-clockwise.la-3x>div{width:24px;height:24px;margin-top:-12px;margin-left:-12px}@-webkit-keyframes ball-spin-clockwise{0%,100%{opacity:1;-webkit-transform:scale(1);transform:scale(1)}20%{opacity:1}80%{opacity:0;-webkit-transform:scale(0);transform:scale(0)}}@-moz-keyframes ball-spin-clockwise{0%,100%{opacity:1;-moz-transform:scale(1);transform:scale(1)}20%{opacity:1}80%{opacity:0;-moz-transform:scale(0);transform:scale(0)}}@-o-keyframes ball-spin-clockwise{0%,100%{opacity:1;-o-transform:scale(1);transform:scale(1)}20%{opacity:1}80%{opacity:0;-o-transform:scale(0);transform:scale(0)}}@keyframes ball-spin-clockwise{0%,100%{opacity:1;-webkit-transform:scale(1);-moz-transform:scale(1);-o-transform:scale(1);transform:scale(1)}20%{opacity:1}80%{opacity:0;-webkit-transform:scale(0);-moz-transform:scale(0);-o-transform:scale(0);transform:scale(0)}}</style>
                <div class="la-ball-spin-clockwise la-2x">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
            
            <script>

                setTimeout(function() {
                    var spinner = document.querySelector('.global-spinner');
                    if (spinner) spinner.style.display = 'flex';
                }, 50);

            </script>

            </pixie-editor>

        </div>	
        
    <?php endif; ?> 
</div>

<?php if ($Settings->get("data.advanced")): ?>    

<script src="<?= PLUGINS_URL."/pixedit/pixie/scripts.min.js?v8"?>"></script>

<script>
        	var pixie = new Pixie({

				<?php if ($Settings->get("data.dialog")): ?>  
					blankCanvasSize: {width: 1080, height: 1350},
				<?php endif; ?>
        		ui: {
        			mode: 'inline',
					theme: '<?= $Settings->get("data.theme") ?>',
					<?php if ($Settings->get("data.dialog")): ?>  
					openImageDialog: {
										show: false,
									},
					<?php endif; ?>
					nav: {
						position: '<?= $Settings->get("data.headerbar") ?>'						
					}
        		},
        		languages: {
        			active: 'multilang',
        			custom: {
        				multilang: {
        					"History": "<?= __("History") ?>",
        					"Objects": "<?= __("Objects") ?>",
        					"Canvas Background": "<?= __("Canvas Background") ?>",
        					"Width": "<?= __("Width") ?>",
        					"Height": "<?= __("Height") ?>",
        					"Brush Color": "<?= __("Brush Color") ?>",
        					"Brush Type": "<?= __("Brush Type") ?>",
        					"Brush Size": "<?= __("Brush Size") ?>",
        					"Cancel": "<?= __("Cancel") ?>",
        					"Close": "<?= __("Close") ?>",
        					"Apply": "<?= __("Apply") ?>",
        					"Size": "<?= __("Size") ?>",
        					"Maintain Aspect Ratio": "<?= __("Maintain Aspect Ratio") ?>",
        					"Use Percentages": "<?= __("Use Percentages") ?>",
        					"Radius": "<?= __("Radius") ?>",
        					"Align Text": "<?= __("Align Text") ?>",
        					"Format Text": "<?= __("Format Text") ?>",
        					"Color Picker": "<?= __("Color Picker") ?>",
        					"Add Text": "<?= __("Add Text") ?>",
        					"Font": "<?= __("Font") ?>",
        					"Upload": "<?= __("Upload") ?>",
        					"Google Fonts": "<?= __("Google Fonts") ?>",
        					"Search...": "<?= __("Search...") ?>",
        					"Shadow": "<?= __("Shadow") ?>",
        					"Color": "<?= __("Color") ?>",
        					"Outline": "<?= __("Outline") ?>",
        					"Background": "<?= __("Background") ?>",
        					"Texture": "<?= __("Texture") ?>",
        					"Gradient": "<?= __("Gradient") ?>",
        					"Text Style": "<?= __("Text Style") ?>",
        					"Delete": "<?= __("Delete") ?>",
        					"Background Color": "<?= __("Background Color") ?>",
        					"Outline Width": "<?= __("Outline Width") ?>",
        					"Blur": "<?= __("Blur") ?>",
        					"Offset X": "<?= __("Offset X") ?>",
        					"Offset Y": "<?= __("Offset Y") ?>",
        					"Open": "<?= __("Open") ?>",
        					"Save": "<?= __("Save") ?>",
        					"Zoom": "<?= __("Zoom") ?>",
        					"Editor": "<?= __("Editor") ?>",
        					"Filter": "<?= __("Filter") ?>",
        					"Resize": "<?= __("Resize") ?>",
        					"Crop": "<?= __("Crop") ?>",
        					"Text": "<?= __("Text") ?>",
        					"Shapes": "<?= __("Shapes") ?>",
        					"Shape": "<?= __("Shape") ?>",
        					"Stickers": "<?= __("Stickers") ?>",
        					"Frame": "<?= __("Frame") ?>",
        					"Transfrom": "<?= __("Transfrom") ?>",
        					"Merge": "<?= __("Merge") ?>",
        					"Draw": "<?= __("Draw") ?>",
        					"Corners": "<?= __("Corners") ?>",
        					"Background Image": "<?= __("Background Image") ?>",
        					"Main Image": "<?= __("Main Image") ?>"
        				}
        			}
                },
                <?php if ($Settings->get("data.gfapikey")): ?>  
                googleFontsApiKey: '<?= $Settings->get("data.gfapikey") ?>',
				<?php endif; ?>
                <?php if ($AuthUser->get("package_id") == 0 && $Settings->get("data.watermark")): ?>  
                watermarkText: '<?= $Settings->get("data.watermarkText") ?>',
				<?php endif; ?>
                <?php if ($AuthUser->get("package_id") != 0 || !$Settings->get("data.save_trial")): ?>  
                onSave: function(data,name){
					saveProcess();
                    pixie.http().post('<?= APPURL."/e/".$idname."/req/savepicture" ?>', {name: name, data: data}).subscribe(function(response){
						console.log(response);
						if(response.success){
							saveSuccess();
						}else{
							saveError();
						}
                    });
                },
                <?php endif; ?>               
        	});
</script>

<?php endif; ?> 

