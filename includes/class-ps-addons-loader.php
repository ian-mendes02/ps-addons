<?php
/**
 * Registers all required plugin hooks.
 * @package    PS_Addons
 * @subpackage PS_Addons/includes
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 */
class PS_Addons_Loader {

	/**
	 * An array of WordPress actions.
	 * @since 1.0.0
	 */
	protected array $actions;

	/**
	 * An array of WordPress filters.
	 * @since 1.0.0
	 */
	protected array $filters;

	public function __construct() {
		$this->actions = [];
		$this->filters = [];
	}

	/**
	 * Adds a new action to the `actions` array to be registered on load.
	 * @param string $hook Action name.
	 * @param object $component Component where callback function is defined.
	 * @param string $callback Callback function identifier.
	 * @param int $priority Callback function priority.
	 * @param int $accepted_args Number of arguments to be passed to the callback function.
	 * @since 1.0.0
	 */
	public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
		$this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
	}

	/**
	 * Adds a new filter to the `filters` array to be registered on load.
	 * @param string $hook Filter name.
	 * @param object $component Component where callback function is defined.
	 * @param string $callback Callback function identifier.
	 * @param int $priority Callback function priority.
	 * @param int $accepted_args Number of arguments to be passed to the callback function.
	 * @since 1.0.0
	 */
	public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
		$this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
	}

	/**
	 * Stores added hooks into a single collection.
	 * @since 1.0.0
	 */
	private function add($hooks, $hook, $component, $callback, $priority, $accepted_args) {
		$hooks[] = [
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		];
		return $hooks;
	}

	/**
	 * Registers queued hooks.
	 * @since 1.0.0
	 */
	public function run() {
		foreach ($this->filters as $hook) {
			add_filter($hook['hook'], [$hook['component'], $hook['callback']], $hook['priority'], $hook['accepted_args']);
		}
		foreach ($this->actions as $hook) {
			add_action($hook['hook'], [$hook['component'], $hook['callback']], $hook['priority'], $hook['accepted_args']);
		}
	}
}
