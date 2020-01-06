<?php


/**
 * Search criteria for {@link AbstractDao}.
 * <p>
 * Can be easily extended without changing the {@link AbstractDao} API.
 */
abstract class AbstractSearchCriteria {

    abstract public function hasFilter();

}
