<?php
	// Hedaer menu data
	function menuOurDivisionSection() {
		$ourDivisionsObjs = \App\Models\OurDivision::where('status',1)
			->whereNull('deleted_at')
			->get();
		return $ourDivisionsObjs;
	}
?>