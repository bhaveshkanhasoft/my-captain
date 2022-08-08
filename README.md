# My Captain

To manage the storing and retrieval of the bookings I have used the Redis caching.

I've made a controller ```app/Http/Controllers/BookingsController.php```, which is managing the storing and fetching the bookings.

In the ```routes/api.php```, I've managed all the required routings.

In the root directory I've attached the ```My Captain.postman_collection.json```, which is containing the API information including Request parameters and URLs for the API.

I've also prepared some tests for the APIs, you can find them at ```tests/Feature/BookingsTest.php```.