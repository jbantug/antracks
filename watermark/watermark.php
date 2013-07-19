<?php
	/*
Plugin Name: Watermark Plugin
Plugin URI:
Description: 
Version: .1
Author: AnyTv
Author URI: 
License:
*/
/*  Copyright 2013  OpenKit 

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

    function watermarkplug() {
      $upload_dir = wp_upload_dir();
      $upload_dir = $upload_dir['basedir'] . "/wm_images";
      wp_mkdir_p($upload_dir); 

      require 'wp-content/plugins/watermark/func/images.func.php';

      if(isset($_POST['url'])){
        $url = $_POST['url'];
        $x = $_POST['xpoint'];
        $y = $_POST['ypoint'];
        $ss = $_POST['size'];
        if($ss[0]!='all'){
          for($t=0;$t<count($ss);$t++){
            $quality[$t]=$ss[$t];
          }
        }
        else{
          $quality[0]='default';
          $quality[1]='mqdefault';
          $quality[2]='hqdefault';
        }

            //$channel = explode("youtube.com/", $url);
        $user = $url;
        $upload_dir = $upload_dir. "/".$user;
        wp_mkdir_p($upload_dir); 
        $link = "http://gdata.youtube.com/feeds/api/users/".$user."/uploads";
        $xml = simplexml_load_file($link);
        $uploads = array();
        $u = 0;

        foreach($xml->entry as $value){
          $temp = $value->id;
          $temp = explode("gdata.youtube.com/feeds/api/videos/", $temp);
          $uploads[$u] = $temp[1];
          $u++;
        }
      for($q=0;$q<count($quality);$q++){
        for($m=0;$m<count($uploads);$m++){
          $image ="http://img.youtube.com/vi/".$uploads[$m]."/".$quality[$q].".jpg";
          $name = basename($image);
          file_put_contents("wp-content/uploads/wm_images/$user/$name", file_get_contents($image));

          $file_name = $name;
          $file_tmp = $name;
          $file_wm = $_FILES['wt']['name'];
          $files = filesize($_FILES["wt"]["tmp_name"]);

          move_uploaded_file($_FILES["wt"]["tmp_name"],"wp-content/uploads/wm_images/".$user.'/'. $file_wm);

          if(allowed_image($file_name)==true){
            $wm_dest = "wp-content/uploads/wm_images/".$user.'/'. $file_wm;
            $file_name = $quality[$q].'_'.md5(microtime(true)).'.png';
            $newfile = watermark_image($user,$file_tmp,'wp-content/uploads/wm_images/'.$user.'/'.$file_name,$wm_dest,$x,$y);

          } else{
            echo '<p>image type not accepted.</p>';
            }
          }
        }
      }

      ?>
      <table>
        <form action="<?php echo $PHP_SELF;?>" method="post" enctype="multipart/form-data">
          <tr>
            <td>
              Input Channel ID:
              <input type="text" id="url" name="url"/>
              <input type="button" value="Retrieve Thumbnail" onclick="previewimg()">
            </td>
            <td>
            </td>
            <tr>
              <td>Apply to thumbnails:
                <input type="checkbox" id="small" name="size[]" value="default" onclick="size_func('small');">Small
                <input type="checkbox" id="medium" name="size[]" value="mqdefault" onclick="size_func('medium');">Medium
                <input type="checkbox" id="large" name="size[]" value="hqdefault" onclick="size_func('large');">Large
                <input type="checkbox" id="all" name="size[]" value="all" onclick="size_func('all');">All
              </td>
            </tr>
            <td>
              <div style="width:400px;height:200px;">
                <div id="waterm" style="position:absolute;">
                </div>
                <div id="output">
                </div>
              </div>
            </td>
            <td>Watermark:
              <input type="file" id="wt" name="wt" accept="image/png"/><p id="heretotest"></p>
            </td>
          </tr>
          <tr>
            <td>
              <input type="button" value="&#9650;" onclick="vertical('up')">
              <input type="text" id='ypoint' name='ypoint' size="1" value="0">
              <input type="button" value="&#9660;" onclick="vertical('down')">
              <input type="button" value="&#9664;" onclick="horizontal('left')">
              <input type="text" id='xpoint' name='xpoint' size="1" value="0">
              <input type="button" value="&#9654;" onclick="horizontal('right')">
              <input type="button" value="&#8722;">
              <input type="text" size="1" value="0">
              <input type="button" value="&#43;">
            </td>
          </tr>
          <tr>
            <td>
              <input type="submit" value="Finalize"/>
                  <!-- x:<input type="button" value="<" onclick="horizontal('left')"/><input type="text" id="horizon" name="horizon" value="0" size="10"/><input type="button" value=">" onclick="horizontal('right')"/><br>
                  y:<input type="button" value="\/" onclick="verti('up')"/><input type="text" id="vertical" name="vertical" value="0" size="10"/><input type="button" value="/\" onclick="verti('down')"/><br> -->  
                </td>
              </tr>
              <tr>
                <td>
                </td>
              </tr>
            </form>
          </table>
          <script language="javascript">
          function size_func(size){
            var catch1 = document.getElementById("small").checked;
            var catch2 = document.getElementById("medium").checked;
            var catch3 = document.getElementById("large").checked;
            if(size=='all' || (catch3==true&&catch2==true&&catch1==true)){
            document.getElementById("small").checked=false;
            document.getElementById("medium").checked=false;
            document.getElementById("large").checked=false;
            document.getElementById("all").checked=true;
            }
            else if(size=='small'){
            document.getElementById("all").checked=false;
            }
            else if(size=='medium'){
            document.getElementById("all").checked=false;
            }
            else if(size=='large'){
            document.getElementById("all").checked=false;
            }
          }

          function horizontal(direction){
            var value = document.getElementById("xpoint").value;
            var value_int = parseInt(value);
            if(direction=='left'){
              if(value_int<=1){
                var value2 = 0;}
                else{value2 = value_int-1;}}
                else if(direction=='right'){
                  var value2 = value_int+1;}
                  document.getElementById("xpoint").value=value2;
                  document.getElementById("waterm").style.marginLeft=value2+"px";
                }

                function vertical(directiony){
                  var value = document.getElementById("ypoint").value;
                  var value_int = parseInt(value);
                  if(directiony=='up'){
                    if(value_int<1){value2=0;}
                    else{var value2 = value_int-1;}}
                    else if(directiony=='down'){
                      var value2 = value_int+1;}
                      document.getElementById("ypoint").value=value2;
                      document.getElementById("waterm").style.marginTop=value2+"px";
                    }

                    function previewm(evt){
                      document.getElementById("waterm").innerHTML="";        

                var files = evt.target.files; // FileList object
                // Loop through the FileList and render image files as thumbnails.
                for (var i = 0, f; f = files[i]; i++) {

                  // Only process image files.
                  if (!f.type.match('image.*')) {
                    continue;
                  }
                  var reader = new FileReader();
                  // Closure to capture the file information.
                  reader.onload = (function(theFile) {
                    return function(e) {
                      // Render thumbnail.
                      var span = document.createElement('span');
                      span.innerHTML = ['<img id="imgwm" class="thumb" style="height:70px;" src="', e.target.result,
                      '" title="', escape(theFile.name), ' "/>'].join('');
                      document.getElementById('waterm').insertBefore(span, null);
                      // var temph = document.getElementById('imgwm').height;
                      // var tempw = document.getElementById('imgwm').width;
                      // document.getElementById("heretotest").innerHTML=temph+"-"+tempw;
                    };
                  })(f);

                  // Read in the image file as a data URL.
                  reader.readAsDataURL(f);
                }
            // var wm = thisis.toString();
            // var wmstr = wm.split("fakepath\\");;
            // document.getElementById("waterm").innerHTML="<img src='..\\"+wmstr[1]+"'/>";
            }
          document.getElementById('wt').addEventListener('change', previewm, false);
          function previewimg(){
            var userid = document.getElementById("url").value;
              // var chuser = userid.split("youtube.com/");
              var xmllink = "http://gdata.youtube.com/feeds/api/users/"+userid+"/uploads";
              if (window.XMLHttpRequest)
                  {// code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp=new XMLHttpRequest();
                  }
              else
                  {// code for IE6, IE5
                    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                  }
                  xmlhttp.open("GET",xmllink,false);
                  xmlhttp.send();
                  xmlDoc=xmlhttp.responseXML;
                  var prv = xmlDoc.getElementsByTagName("id")[1].childNodes[0].nodeValue;
                  var prvstr = prv.toString();
                  var prvimg = prvstr.split("youtube.com/feeds/api/videos/");
                  document.getElementById("output").innerHTML= "<img src='http://img.youtube.com/vi/"+prvimg[1]+"/mqdefault.jpg'/>";
                }
                </script>
                <?php
                $images = array();
                $imagesfinal = array();
                $i=0;
                $n=0;

                
                if(isset($newfile)){
                  unlink($wm_dest);
                  $dir = new DirectoryIterator("wp-content/uploads/wm_images/$user");
                  foreach ($dir as $fileinfo) {
                    $images[$i] = $fileinfo->getFilename();
                    $temp = $images[$i];
                    $i++;
                  }
                  echo "<div style='overflow-y:scroll;overflow-x:hidden;width:345px;height:200px'><div style='width:400px;height:250px'>These items are located at <br>wp-content/uploads/wm_images/".$user."<br>";
                  for($j=2;$j<count($images);$j++){
                    echo "<p style='display:inline-block;'><img src='wp-content/uploads/wm_images/".$user.'/'.$images[$j]."'/></p>";
                  }
                  echo"</div></div>";
                  echo "<form action='' method='POST'>
                  <input type='submit' value='UPLOAD'/>
                  </form>";
                }
              }
              add_shortcode('watermark','watermarkplug');
              ?>