<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

//Awwward
$factory->define(CMV\Models\AwwwardsScraper\Awwward::class, function (Faker\Generator $faker) {
    return [
        'username'=> $faker->userName,
        'name'=> $faker->name(null),
        'site_url'=> $faker->url,
        'twitter'=> $faker->domainWord,
        'gplus'=> 'http://plus.google.com/'.$faker->domainWord,
        'facebook'=> 'http://facebook.com/'.$faker->domainWord,
        'country'=> $faker->country,
        'city'=> $faker->city
    ];
});

//Awwwcategory
$factory->define(CMV\Models\AwwwardsScraper\AwwwCategory::class, function (Faker\Generator $faker) {
    return [
        'name'=> $faker->word
        
    ];
});

//ConciergeSite
$factory->define(CMV\Models\PM\ConciergeSite::class, function (Faker\Generator $faker) {
    return [
        //'files' => [],
        'type' => $faker->randomElement(CMV\Models\PM\ConciergeSite::$types),
        'bitbucket_slug' => null,
        'name' => implode(' ', $faker->words(4)),
        'slug' => implode('-', $faker->words(4)),
        'url' => $faker->url,
        'saved_credentials' => Crypt::encrypt(json_encode([
            implode(' ',$faker->words(2)) => implode(' ',$faker->words(5)),
            implode(' ',$faker->words(3)) => implode(' ',$faker->words(8)),
            implode(' ',$faker->words(1)) => implode(' ',$faker->words(10))
        ])),
        'subdomain' => implode('-', $faker->words(4))//subdomain for the staging site {subdomain}.concierge.approvemyviews.com
    ];
});

//ToDo
$factory->define(CMV\Models\PM\ToDo::class, function (Faker\Generator $faker) {
    return [
        'reference_type' => 'project'
    ];
});

//Invoice
$factory->define(CMV\Models\PM\Invoice::class, function (Faker\Generator $faker) {
    return [
        'discount' => $faker->numberBetween(0,100),
        'status' => $faker->randomElement(CMV\Models\PM\Invoice::$statuses),
        'date_paid' => $faker->dateTime()
    ];
});

//Line Item
$factory->define(CMV\Models\PM\LineItem::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->text(10),
        'description' => $faker->sentence(8),
        'price' => $faker->numberBetween(500,7500),
        'category' => $faker->randomElement(CMV\Models\PM\LineItem::$categories)
    ];
});

//Thread
$factory->define(CMV\Models\PM\Thread::class, function(Faker\Generator $faker) {
    return [
        'message_count' => 0
    ];
});

//Message
$factory->define(CMV\Models\PM\Message::class, function (Faker\Generator $faker) {
    return [
        'content' => $faker->realText( $faker->numberBetween(200,500) )
    ];
});

//Project
$factory->define(CMV\Models\PM\Project::class, function (Faker\Generator $faker) {
    return [
        'git_url' => 'ssh@bitbucket.org/codemyviews/'.implode('-', $faker->words(4)), //the ssh url for the git repo on bitbucket
        'bitbucket_slug' => null, //bitbucket ID of the project for API purposes
        'team_id' => $faker->url,
        'name' => implode(' ', $faker->words(4)), //unique name of project
        'slug' => implode('-', $faker->words(4)), //unique
        'requested_deadline' => $faker->randomElement(CMV\Models\PM\Project::$requestedDeadlineOptions),
        'guaranteed_deadline' => $faker->randomElement(null, $faker->dateTimeThisMonth()),
        'status' => $faker->randomElement(CMV\Models\PM\Project::$statuses),
        'subdomain' => implode('-', $faker->words(4))
    ];
});

//ProjectBrief
$factory->define(CMV\Models\PM\ProjectBrief::class, function (Faker\Generator $faker) {
    $type = array_rand(['wordpress', 'frontend', 'other']);

    return [
        'text' => json_encode(['brief_type' => $type]),
        'created_by_id' => 1
//        'approved_by_customer_at' => $faker->randomElement([null, $faker->dateTimeThisMonth()])
    ];
});

//ProjectType
$factory->define(CMV\Models\PM\ProjectType::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->randomElement(CMV\Models\PM\ProjectType::$defaults)
    ];
});



//Activity
$factory->define(CMV\Models\Prospector\Activity::class, function (Faker\Generator $faker) {
    return [
        'content' => $faker->realText( $faker->numberBetween(200,800) ),
        'created_at' => $faker->dateTimeThisYear()
    ];
});

//Company
$factory->define(CMV\Models\Prospector\Company::class, function (Faker\Generator $faker) {
    return [
        'name' => implode(' ', $faker->words(4)),
        'type' => $faker->randomElement(CMV\Models\Prospector\Company::$types),
        'status' => $faker->randomElement(CMV\Models\Prospector\Company::statusKeys())
    ];
});

//CompanyMeta
$factory->define(CMV\Models\Prospector\CompanyMeta::class, function (Faker\Generator $faker) {
    return [
        'value' => $faker->realText( $faker->numberBetween(10,20) )
    ];
});

//Contact
$factory->define(CMV\Models\Prospector\Contact::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName(null),
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->email
    ];
});

//ContactMeta
$factory->define(CMV\Models\Prospector\ContactMeta::class, function (Faker\Generator $faker) {
    return [
        'value' => $faker->realText( $faker->numberBetween(10,20) )
    ];
});

//Users
$factory->define(CMV\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->email,
        'password' => bcrypt('password'),
        'remember_token' => str_random(10),
    ];
});
$factory->defineAs(CMV\User::class, 'developer', function (Faker\Generator $faker) use ($factory) {

    $user = $factory->raw(CMV\User::class);
    return array_merge($user, ['is_developer' => true,'email' => 'developer-'.$faker->unique()->numberBetween(0,9).'@cmv.local']);
});

$factory->defineAs(CMV\User::class, 'project_manager', function (Faker\Generator $faker) use ($factory) {
    
    $user = $factory->raw(CMV\User::class);
    return array_merge($user, ['is_admin' => true,'email' => 'admin-'.$faker->unique()->numberBetween(20,30).'@cmv.local']);
});

$factory->defineAs(CMV\User::class, 'sales_rep', function (Faker\Generator $faker) use ($factory) {
    
    $user = $factory->raw(CMV\User::class);
    return array_merge($user, ['is_sales_rep' => true,'email' => 'sales-'.$faker->unique()->numberBetween(10,20).'@cmv.local']);

});


//Team
$factory->define(CMV\Team::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company,
    ];
});




