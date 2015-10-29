<?php
/**
 * Plugin Name: Import User From CSV
 * Plugin URI:  http://izotx.com
 * Description: This plugin adds users from the CSV file
 * Version: 1.0.0
 * Author: Janusz Chudzynski
 * Author URI: http://izotx.com
 * License: GPL3
 */
 
 /*
  Import User From CSV Plugin
  Copyright (C) 2015, Janusz Chudzynski
  Contact: janusz@izotx.com 

  Import User From CSV Plugin is distributed under the GNU General Public License, Version 3,
  June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
  St, Fifth Floor, Boston, MA 02110, USA

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
  ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
  ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */


//Comment it out if you don't want to import the users every time plugin is initialized
add_action('init', 'addStudents');
function addStudents(){
	//gets the file name from students_data directory
	$file2 = dirname(__FILE__)."/students_data/students.csv";	
	//parse, process, and add users
	processCSVFile($file2,",");
}

/**Processes CSV file, like students csv*/
function processCSVFile($filename='', $delimiter=',')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
   						$rowData = array();
						$rowData[]= $row[0] ;//name
						$rowData[]= $row[1];//last name	
						$rowData[] =strtolower(substr(trim($row[0]),0,1).trim($row[1]));//user_id
						$random_password = wp_generate_password( $length=8, $include_standard_special_chars=false );
						$rowData[] = $random_password;//password
						$rowData[] = $row[2];//email
						insertUser($rowData);
	        }
        fclose($handle);
    }
}

/**Inserts User $row is an array with user data*/
function insertUser($row){

	$firstname = $row[0];	
	$lastname = $row[1];
	$username = $row[2];
	$password = $row[3];
	$email = $row[4];
	
	
	
	$user_id = username_exists( $username );
	if(!$user_id){
		$createdId = wp_create_user( $username, $password,$email);
		echo "\n \n Password for ".$username ."is: ".$password;
		
		if($createdId){//Update user,  wp_create_user can't add the first or last name of the user
			wp_update_user( array( 'ID' => $createdId, 'first_name' => $firstname, 'last_name' => $lastname, 'role'=>'contributor' ) );
			echo "\nUser Updated";
			}
	}
	else{
		//echo "<br> User Exists: ".$username;
	}
}

?>