<?
use Carbon\Carbon;

/**
 * Extension de la classe Carbon pour permettre la traduction des dates
 */
TranslatableDateTime:Carbon

	WEEK_PER_MONTH = 4;

	+ __get $name
		$key = 'date.format.' . $name;
		$format = §($key);
		if $key !== $format
			<>formatLocalized($format) ?: >format('date.altFormat.' . $name);
		else
			unset($key, $format);
			$method = 'to' . studly_case($name) . 'String';
			if method_exists($this, $method)
				<>$method();
			else
				<parent::__get($name);

	+ __toString
		<>longDateTime;

	/**
	 * Format the instance as a difference for human if recent, or a date else
	 *
	 * @return string
	 */
	+ toRecentDateString
		<>isToday() ?
			§('date.today') : (
			>isYesterday() ?
				§('date.yesterday') :
				>longDate
			);

	/**
	 * toRecentDateString() with an uppercase at first character
	 *
	 * @return string
	 */
	+ toURecentDateString
		<ucfirst(>toRecentDateString());

	/**
	 * Format the instance as a difference for human if recent, or a time else
	 *
	 * @return string
	 */
	+ toRecentTimeString
		<>diffInSeconds() < 3600 * 1.5 ?
			>diffForHumans() :
			trim(>rTime);

	/**
	 * Format the instance as a readable date
	 *
	 * @return string
	 */
	+ toPreferredString
		<>formatLocalized('%c');

	/**
	 * Get the difference in a human readable format.
	 *
	 * When comparing a value in the past to default now:
	 * 1 hour ago
	 * 5 months ago
	 *
	 * When comparing a value in the future to default now:
	 * 1 hour from now
	 * 5 months from now
	 *
	 * When comparing a value in the past to another value:
	 * 1 hour before
	 * 5 months before
	 *
	 * When comparing a value in the future to another value:
	 * 1 hour after
	 * 5 months after
	 *
	 * @param  Carbon  $other
	 *
	 * @return string
	 */
	+ diffForHumans Carbon $other = null
		$isNow = $other === null;

		if $isNow
			$other = static::now($this->tz);

		$isFuture = >gt($other);

		$delta = $other->diffInSeconds($this);

		$divs = array(
			'second' => self::SECONDS_PER_MINUTE,
			'minute' => self::MINUTES_PER_HOUR,
			'hour' => self::HOURS_PER_DAY,
			'day' => self::DAYS_PER_WEEK,
			'week' => self::WEEK_PER_MONTH,
			'month' => self::MONTHS_PER_YEAR
		);

		$unit = 'year';

		foreach $divs as $divUnit => $divValue
			if $delta < $divValue
				$unit = $divUnit;
				break;

			$delta = floor($delta / $divValue);

		if $delta == 0
			$delta = 1;

		<§('date.' .(
			$isNow ?
				($isFuture ? 'fromNow' : 'ago') :
				($isFuture ? 'after' : 'before')
			), array(
				'diff' => $delta . ' ' . §('date.' . $unit, $delta)
			)
		);

?>