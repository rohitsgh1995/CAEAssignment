# CAE Assignment

## Local Setup

1. **Setting Up Your Environment**
   - Open your `.env` file and add these lines:
     ```
     DB_CONNECTION=sqlite
     QUEUE_CONNECTION=database
     ```
   - Remove or comment out these lines if they exist:
     ```
     DB_HOST
     DB_PORT
     DB_DATABASE
     DB_USERNAME
     DB_PASSWORD
     ```

2. **Migrating the Database**
   - After updating the `.env` file, run:
     ```bash
     php artisan migrate
     ```
   - If asked, type 'yes' to create the database.

## Postman Setup

1. **Using Postman**
   - Import the provided Postman collection file from ./postman/CAEAssignment.postman_collection

## Usage

1. **Interacting with the API**
   - Explore the API endpoints for the CAE system.

2. **Testing with Postman**
   - Use the imported Postman collection to test the API endpoints.

3. **Documentation**
   - Refer to the documentation within Postman for guidance on using each endpoint.

4. **Troubleshooting**
   - If you run into any issues, check the documentation or contact the development team for help.

## Conclusion

The CAE Assignment API is ready for testing. Follow the instructions to get started. If you need assistance, feel free to reach out. Happy testing!
