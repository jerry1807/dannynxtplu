<?php if (!defined('APP_VERSION')) {
   die("Yo, what's up?");
   }
   ?>
<div class='skeleton' id="account">
   <form class="js-ajax-form"
      action="<?=APPURL . "/e/" . $idname . "/settings"?>"
      method="POST">
      <input type="hidden" name="action" value="save">
      <div class="container-1200">
         <div class="row clearfix">
            <div class="form-result">
            </div>
            <div class="col s12 m6 l6">
               <section class="section">
                  <div class="section-header clearfix">
                     <h2 class="section-title mb-25"><?=__("Settings")?></h2>
                  </div>
                  <div class="section-content">
                     <div class="mb-40">
                        <label class="form-label"><?=__("Endpoint Url")?></label>
                        <input class="input"
                           name="endpoint"
                           type="text"
                           value="<?=htmlchars($Settings->get("data.endpoint"))?>"
                           placeholder="<?=__("Pixie Endpoint")?>"
                           maxlength="100">
                        <ul class="field-tips">
                           <li><?=__('Purchase the Endpoint script <a href="https://codecanyon.net/item/pixie-image-editor/10721475">Pixie - Image Editor</a> and change this url accordingly. ')?></li>
                           <li><?=__('You can use any free Pixie Editor, as demo endpoint you could use https://pixie.vebto.com/final/')?></li>
                           <li><?=__('If you want to use the Advanced mode and its features, you need to own Pixie and copy the App structur into the module directory.')?></li>
                           <li><?=__('The path is "/inc/plugins/pixedit/pixie/*", once you have placed the files, set the endpoint to "https://yourdomain.com/inc/plugins/pixedit/pixie/"')?></li>                                             
                        </ul>
                     </div>

                    <div class="mb-20">
                        <label>
                           <input type="checkbox"
                              class="checkbox"
                              name="googleanalytics"
                              value="1"
                              <?=$Settings->get("data.googleanalytics") ? "checked" : ""?>>
                           <span>
                              <span class="icon unchecked">
                              <span class="mdi mdi-check"></span>
                              </span>
                              <?=__('Remove Google Analytics Fragment')?>
                           </span>
                        </label>
                     </div>
                  </div>

                  <input class="fluid button button--footer" type="submit" value="<?=__("Save")?>">
               </section>
            </div>

            <div class="col s12 m6 l6 m-last l-last">
               <section class="section">
                  <div class="section-header clearfix">
                     <h2 class="section-title mb-25"><?=__("Advanced Mode")?></h2>
                  </div>
                  <div class="section-content">

                     <div class="mb-20 clearfix">
                        <div class="col s12 m6 l6">
                        <label>
                           <input type="checkbox"
                              class="checkbox"
                              name="advanced"
                              value="1"
                              <?=$Settings->get("data.advanced") ? "checked" : ""?>>
                           <span>
                              <span class="icon unchecked">
                              <span class="mdi mdi-check"></span>
                              </span>
                              <?=__('Activate Advanced Mode')?>
                           </span>
                        </label>                           
                        </div>
                        <div class="col s12 m6 m-last l6 l-last mb-20">
                        <label>
                           <input type="checkbox"
                              class="checkbox"
                              name="dialog"
                              value="1"
                              <?=$Settings->get("data.dialog") ? "checked" : ""?>>
                           <span>
                              <span class="icon unchecked">
                              <span class="mdi mdi-check"></span>
                              </span>
                              <?=__('Disable Openimage Dialogbox')?>
                           </span>
                        </label>                            
                        </div>
                     </div>   

                     <div class="mb-20 clearfix">
                        <div class="col s6 m6 l6">

                           <label>
                           <input type="checkbox"
                              class="checkbox"
                              name="watermark"
                              value="1"
                              <?=$Settings->get("data.watermark") ? "checked" : ""?>>
                           <span>
                           <span class="icon unchecked">
                           <span class="mdi mdi-check"></span>
                           </span>
                           <?=__('Enable Watermark for trials.')?>
                           </span>
                           </label>
                    
                        </div>
                        <div class="col s6 s-last m6 m-last l6 l-last mb-20">
                            <label>
                              <input type="checkbox"
                                 class="checkbox"
                                 name="save_trial"
                                 value="1"
                                 <?=$Settings->get("data.save_trial") ? "checked" : ""?>>
                              <span>
                                 <span class="icon unchecked">
                                 <span class="mdi mdi-check"></span>
                                 </span>
                                 <?=__('Disable Mediathek Saving for trials.')?>
                              </span>
                           </label>
                        </div>
                     </div>
                     <div class="mb-30">
                        <label class="form-label"><?=__("Watermark Text")?></label>
                        <input class="input"
                           name="watermarktext"
                           type="text"
                           value="<?=htmlchars($Settings->get("data.watermarktext"))?>"
                           placeholder="<?=__("Codingmatters")?>"
                           maxlength="100">
                        <ul class="field-tips">
                           <li><?=__('Watermark text')?></li>
                        </ul>
                     </div>
                     <div class="mb-30">
                        <label class="form-label"><?=__("Google Fonts Apikey")?></label>
                        <input class="input"
                           name="gfapikey"
                           type="text"
                           value="<?=htmlchars($Settings->get("data.gfapikey"))?>"
                           placeholder="<?=__("Apikey")?>"
                           maxlength="100">
                        <ul class="field-tips">
                           <li><?=__('Google Fonts Apikey or leave blank.')?></li>
                        </ul>
                     </div>
                     <div class="mb-10 clearfix">
                        <div class="col s6 m6 l6">
                           <label class="form-label"><?=__("Theme")?></label>
                           <select name="theme" class="input">
                              <?php
                                 $skin_color = $Settings->get("data.theme")
                                 ?>
                              <option value="light" <?=$Settings->get("data.theme") == "light" ? "selected" : ""?>>
                                 <?=__("Light")?>
                              </option>
                              <option value="dark" <?=$Settings->get("data.theme") == "dark" ? "selected" : ""?>>
                                 <?=__("Dark")?>
                              </option>
                           </select>
                        </div>
                        <div class="col s6 s-last m6 m-last l6 l-last mb-20">
                           <label class="form-label"><?=__("Headerbar Position")?></label>
                           <select name="headerbar" class="input">
                              <option value="top" <?=$Settings->get("data.headerbar") == "top" ? "selected" : ""?>>
                                 <?=__("Top")?>
                              </option>
                              <option value="bottom" <?=$Settings->get("data.headerbar") == "bottom" ? "selected" : ""?>>
                                 <?=__("Bottom")?>
                              </option>
                           </select>
                        </div>
                     </div>
                  </div>
                  <input class="fluid button button--footer" type="submit" value="<?=__("Save")?>">
               </section>
            </div>

         </div>
      </div>
   </form>
</div>