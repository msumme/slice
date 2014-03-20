slice
=====

Doctrine2 ORM Search tool - get a slice of results by applying filters.  

The primary usage of this library is to create pageable searchable front-facing interfaces, by combining it with a tool
to parse URLs into filters to apply to searches.

The initial problem that was solved by this library was very slow counting for InnoDB databases when using Doctrine's Pager class and complex joined filtering.

This library solves that problem by removing the DQL from the interface, which allows for managing the construction of multiple efficient queries.  This in turn allows for 
reliable counting and paging for one-to-many and many-to-many joins.

Those searches can then be used to create aggregated reports.  

Usage
-----


```php
	
	//Assume we have 2 classes
	namespace Acme\Demo\DemoBundle;
	
	class Contact {
		
		protected $id;
		
		protected $name;
		
		//one-to-many
		protected $addresses
	}
	
	class Address {
		
		protected $street;
		
		protected $zip;
	}


	$em = ...; \\Get entity manager

	use Slice\Search\Search;
	$search = new Search($em, 'AcmeDemoBundle:Contact'); //or 'Acme\Demo\DemoBundle' - full name
	
	//Only get contacts with addresses 00211 zip code.
	$search->addFilter(new EqualFilter('addresses.zip', '00211'));
	
	//order Contacts by their Address.street alphabetically ascending. 
	$search->addFilter(new OrderByFilter('addresses.street', 'ASC'));
	
	//get first 10 results
	$search->addFilter(new PagingFilter(0, 10));
	
	$results = $search->getResults();
	
	$totalResults = $search->getTotalResultCount();
	
	echo "$totalResults records were found.\n";
	
	foreach($results as $contact) {
		$addresses = $contact->getAddresses();
	
		print $contact->getName() . "\n";
		foreach($addresses as $address) {
			print "\t" . $address->getStreet() ."\n";
		} 
	}
	
	// output paging links to allow various paging filters to be applied.
	

```

Output would be something like this..
```
Joe Josephson
	111 Address Place. 
James Jameson
	1234 Place St.
	555 Five St.

...
(10th result)
Dan Danson
	94 ABC St.

```

LogicalFilter (AndFilter, OrFilter) allow for grouping filters together into complex logic.

DistanceCriteriaFilter and DistanceSortFilter allow for distance searches against particular latitude/longitudes.

Multiple OrderByFilter instances can be added to a given search, but only 1 PagingFilter is used (the last given).

Finally - ResultSetBuilderInterface objects allow for a particular result to be built instead of the default Entity result set.

The FormulaFilter allows for complicated queries that aren't easily (or conveniently) expressed otherwise - however it is not safe to be exposed to the client via any web interface.  
It can be used to limit the initial result set in interesting ways. 

Example: 
```php

//using search above.
$resultBuilder = new FlatResultBuilder();
$resultBuilder->addSelect('name', 'alias');

$results = $search->getResults($resultBuilder); 
// returns array(0 => array('alias' => 'Joe Josephson'), 1 => array('alias' => 'James Jameson'), 2 => array('alias' => 'Dan Danson') )



```

