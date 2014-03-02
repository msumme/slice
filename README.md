slice
=====

Doctrine2 ORM Search tool - get a slice of results by applying filters.  

This tool will also supply (in the near future) a basic url parser to allow for filters to be added to the url (for client side searches.)

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
	
	foreach($results as $contact) {
		$addresses = $contact->getAddresses();
	
		print $contact->getName() . "\n";
		foreach($addresses as $address) {
			print "\t" . $address->getStreet() ."\n";
		} 
	}
	

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

Example: 
```php

//using search above.
$resultBuilder = new FlatResultBuilder();
$resultBuilder->addSelect('name', 'alias');

$results = $search->getResults($resultBuilder); 
// returns array(0 => array('alias' => 'Joe Josephson'), 1 => array('alias' => 'James Jameson'), 2 => array('alias' => 'Dan Danson') )



```

