<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 11/25/2018
 * Time: 3:25 PM
 */

class zip
{
	private $zip;
	public function __construct( $file_name, $zip_directory)
	{
		$this->zip = new ZipArchive();
		$this->path = dirname( __FILE__ ) . $zip_directory . $file_name;
		$this->zip->open( $this->path, ZipArchive::CREATE );
	}

	/**
	 * Get the absolute path to the zip file
	 * @return string
	 */
	public function get_zip_path()
	{
		return $this->path;
	}

	/**
	 * Add a directory to the zip
	 * @param $directory
	 */
	public function add_directory( $directory )
	{

		if( is_dir( $directory ) && $handle = opendir( $directory ) )
		{
			$this->zip->addEmptyDir( $directory );
			while( ( $file = readdir( $handle ) ) !== false )
			{
				if (!is_file($directory . DIRECTORY_SEPARATOR . $file))
				{
					if (!in_array($file, array('.', '..')))
					{
						$this->add_directory($directory . DIRECTORY_SEPARATOR . $file );
					}
				}
				else
				{
					$this->add_file($directory . DIRECTORY_SEPARATOR . $file);                }
			}
		}
	}

	/**
	 * Add a single file to the zip
	 * @param string $path
	 */
	public function add_file( $path )
	{
		$this->zip->addFile( $path, $path);
	}

	/**
	 * Close the zip file
	 */
	public function save()
	{
		$this->zip->close();
	}
}