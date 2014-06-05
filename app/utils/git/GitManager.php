<?php

/**
 * Git Interface Class
 * https://github.com/kbjr/Git.php/blob/master/Git.php
 *
 * This class enables the creating, reading, and manipulation
 * of git repositories.
 *
 * @class  Git
 */
class GitManager {

	/**
	 * Git executable location
	 *
	 * @var string
	 */
	protected static $bin = 'git';

	/**
	 * Sets git executable path
	 *
	 * @param string $path executable location
	 */
	public static function set_bin($path) {
		self::$bin = $path;
	}

	/**
	 * Gets git executable path
	 */
	public static function get_bin() {
		return self::$bin;
	}

	/**
	 * Sets up library for use in a default Windows environment
	 */
	public static function windows_mode() {
		self::set_bin('git');
	}

	/**
	 * Create a new git repository
	 *
	 * Accepts a creation path, and, optionally, a source path
	 *
	 * @access  public
	 * @param   string  repository path
	 * @param   string  directory to source
	 * @return  GitRepo
	 */
	public static function &create($repo_path, $source = null) {
		return GitRepo::create_new($repo_path, $source);
	}

	/**
	 * Open an existing git repository
	 *
	 * Accepts a repository path
	 *
	 * @access  public
	 * @param   string  repository path
	 * @return  GitRepo
	 */
	public static function open($repo_path) {
		return new GitRepo($repo_path);
	}

	/**
	 * Clones a remote repo into a directory and then returns a GitRepo object
	 * for the newly created local repo
	 * 
	 * Accepts a creation path and a remote to clone from
	 * 
	 * @access  public
	 * @param   string  repository path
	 * @param   string  remote source
	 * @return  GitRepo
	 **/
	public static function &clone_remote($repo_path, $remote) {
		return GitRepo::create_new($repo_path, $remote, true);
	}

	/**
	 * Checks if a variable is an instance of GitRepo
	 *
	 * Accepts a variable
	 *
	 * @access  public
	 * @param   mixed   variable
	 * @return  bool
	 */
	public static function is_repo($var) {
		return (get_class($var) == 'GitRepo');
	}

}