# RDFanimate #

*RDFanimate* is a template engine which uses HTML annotated with RDFa attributes to render data provided by a view model.

## Deprecated ##

This project has been deprecated and replaced by [tempan]. It won't be maintained any further.

[tempan]: http://github.com/rtens/tempan

## Purpose ##

This project was inspired by Iain Dooleys post on [workingsoftware.com]. In his post, Iain argues that "As soon as I'm looking at more than one programming or markup language in the same file, I'm looking at spaghetti code." He refers to the mix-up of a templating language and HTML with most templating engines. He suggests that instead one should use HTML itself to bear the meta-data needed to manipulate the HTML on DOM level. He calls this *Template Animation*, hence the name of this project.

The main advantage is the *template* resulting to be completely independent of the rendering engine. Instead of creating a template with all kind of curly placeholders, the markup looks like this

	<div class="person">
		Name: <span class="name">John Wayne</span>
		Homepage: <a class="homepage" href="http://johnwayne.com">johnwayne.com</a>
	</div>
	
Now we could use the `class` attributes to replace the content of the corresponding elements. But instead I opted to use the attributes of in the *Resource Description Framework in attributes* or [RDFa].

	<div vocab="http://schema.org/" rel="Person">
		Name: <span property="name">John Wayne</span>
		Homepage: <span property="url" href="http://johnwayne.com">johnwayne.com</span>
	</div>
	
The goal of RDFanimate is to *animate* an RDFa annotated template based on data provided by a view model. From the resulting markup an RDF graph can be extracted (see [RDFa/Play]) which is topographically identical with the view model which would be in the above case

	{
		"Person": {
			"name": "John Wayne",
			"url" : "http://johnwayne.com"
		}
	}

[workingsoftware.com]: http://www.workingsoftware.com.au/page/Your_templating_engine_sucks_and_everything_you_have_ever_written_is_spaghetti_code_yes_you
[RDFa]: http://rdfa.info/
[RDFa/Play]: http://rdfa.info/play/

## Main Features ##

The feature set is oriented on [mustache] and consists of the following. 

*Note*: although JSON syntax is used to describe the view models, they are actually PHP objects or Lists.

[mustache]: http://mustache.github.com/

### Replace content with static values ###

*Variables.* The text content and attributes of an element is replaced with the value of the view model matching the name in the `property` attribute.

	{ 
		"one": "Hello", 
		"two": { 
			"value": "World",
			"title": "Everyone"
		}
	}
	
leads to
	
	<span property="one">Hello</span>
	<span property="two" title="Everyone">World</span>

### Navigate complex data ###

*Sections.* Nested data structures can be traversed using the `rel` attribute. To access the inner data of the following view model

	{
		"outer": {
			"inner": {
				"message": "Hello World"
			}
		}
	}
	
one of the following templates can be used

	<div rel="outer"><span rel="inner"><span property="message">Hello World</span></span></div>
	
	<div rel="outer"><span refl="inner" property="inner">Hello World</span></div>

### Replace content with dynamic values ###

*Lambdas.* The referenced data may be the return value of a method or closure. Only zero-arguments methods are possible. Closures receive the parent model as their only argument. This way, dependent and thus redundant data can be calculated on-demand.

	{
		"number": {
			"value": 2,
			"isMany": function (this) { return this.value != 1 }
		}
	}

can be used with

    <span property="number">2</span> car<span rel="number" property="isMany">s</span>

### Remove elements ###

*Conditional sections.* If the value is `false` or `null`, the corresponding element will be removed. If the value is `true` or the field not existing, the element won't be modified.

### Repeat elements ###

*Lists.* If the value of a field is a list, the element will be repeated for each item of the list.

	{
		"pets": {
			"count": {
				"value": 2,
				"isMany": true
			},
			"pet": [
				{ "name": "Cat" },
				{ "name": "Dog" }
			]
		}
	}
	
Siblings of the element in the template will be removed before repeating the element. Thus the following rendered result can be used as its template as well.
	
	<div rel="pets">
		<p>		
			I have <span property="count">2</span> pet<span rel="count" property="isMany">s</span>
		</p>
		<ul>
			<li rel="pet">
				<span property="name">Cat</span>
			</li>
			<li rel="pet">
				<span property="name">Dog</span>
			</li>
		</ul>
	</div>

## Installation ##

There are three options. If you already have [Composer], you can use

	php composer.phar create-project rtens/rdfanimate

to check out RDFanimate as a stand-alone project (you'll need git and php as well). To run the test suite use
	
	cd rdfanimate
	phpunit
	
If you don't have Composer yet, or want to install a different branch you can use

    git clone https://github.com/rtens/rdfanimate.git
    cd rdfanimate
    php install.php

To use it in your own project, add the following lines to your `composer.json`.

    "require" : {
        "rtens/rdfanimate" : "*"
    },
    "minimum-stability": "dev"
	
[Composer]: http://getcomposer.org/

## Basic Usage ##

For a complete description of all features and usage examples, check out the test cases in the [spec] folder. You can find an example using all the basic features together in [ComplexTest.php].

[spec]: https://github.com/rtens/rdfanimate/tree/master/spec/rtens/rdfanimate
[ComplexTest.php]: https://github.com/rtens/rdfanimate/tree/master/spec/rtens/rdfanimate/ComplexTest.php
		
