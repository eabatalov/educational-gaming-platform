MODEL OBJECTS VALIDATION

Override CFormModel::rules() method to specify user input validation rules.

To check for crazy conditions which appear due to coding mistakes throw
InvalidArgumentException. Examples of such coding mistakes:
- Passing data of invalid type
- Adding current customer to his friends
- Adding user with non customer role as a friend
In all this cases website user is nor responsible for getting a way to do such
strange thing. It is responsibility of programmer not to present him such
facilities in website interface. Validation rules shouldn't report customer that
programmer made a mistake.