# dream-hostel

You may be able to access the website using UTM WiFi by copying the following link into your browser:  
10.206.101.189:8080/dreamhostel3/login.php  
Hope it works!

**Problem**  
Due to the quota and filter system, not every student gets to choose to stay at their desired hostel. Annually, students tend to exchange hostels through social media platforms like Telegram, Facebook and etc. It is a very inefficient way for students who want to exchange their hostel. 

**Solution**  
A webiste to help these students to match with others to get their desired college. Example: A to B, B to C, C to A

**Tools and Languages**
- XAMPP
- PHPMyAdmin
- VSCode
- HTML
- CSS
- PHP
- SQL

## Registration
<img width="940" alt="image" src="https://github.com/Yuylam/dream-hostel/assets/147635499/7d068d6d-2285-4720-ba7e-0be24cb8ba99">
<img width="937" alt="image" src="https://github.com/Yuylam/dream-hostel/assets/147635499/92b3fcdf-321d-47f7-9465-d4481fb7887b">

## Login
<img width="950" alt="image" src="https://github.com/Yuylam/dream-hostel/assets/147635499/db493c31-1726-4dee-8cc1-59af78320b43">

## Phases
We are going to separate the matching process to 5 phases.  

### 1. No record found
This is when the user has not entered any details about the college they get and their desired college.
<img width="951" alt="image" src="https://github.com/Yuylam/dream-hostel/assets/147635499/7b38e205-89a5-41fc-86d0-88f900ff70c9">

They will need to start a new match.
<img width="935" alt="image" src="https://github.com/Yuylam/dream-hostel/assets/147635499/040af31b-f943-4290-b428-4c54b850b37e">
<img width="936" alt="image" src="https://github.com/Yuylam/dream-hostel/assets/147635499/18598459-ca13-4a5a-9c88-0eeb7ee4a880">

### 2. Matching
After they have entered the details, if there is no match, their information will be stored in our database to wait for possible match later.
![image](https://github.com/Yuylam/dream-hostel/assets/147635499/cf3e829c-b74a-488c-9752-f63cd91f0833)

### 3. Pending Confirmation
If there is a match found, they need to choose to accept or decline the match.
![image](https://github.com/Yuylam/dream-hostel/assets/147635499/9e82fad3-3b10-4c6c-bfac-13b908f16e7b)
The user can see the contact information of the their match and choose to accept or decline the match.
![image](https://github.com/Yuylam/dream-hostel/assets/147635499/b03efdce-b488-4f20-a33e-123530096739)

### 4. Pending Match Confirmation
If the user has already accepted while his/her match hasn't.
![image](https://github.com/Yuylam/dream-hostel/assets/147635499/542d7420-6437-43a6-a32d-987f7753c4c8)

### 5. Successful
When all the match have acccepted the match, their status will be updated to successful.
![image](https://github.com/Yuylam/dream-hostel/assets/147635499/3bc8e6f5-9a45-4792-9edd-2dbf354024b6)

## Logout
<img width="948" alt="image" src="https://github.com/Yuylam/dream-hostel/assets/147635499/8a3cc88e-067c-446d-8007-21a7e0d8bf14">

## Matching mechanism
- Each user are unique by their matric number  
- Each exchange are unique by their exchangeID  

### Pair match
A -> B & B -> A  
When there is a new entry, we are going to search through our database to find if there is other users that can do exchange we the user.  

**Conditions:**
- Current college (A) = Desired college (B)
- Desired college (A) = Current college (B)
- Gender (A) = Gender (B)
- Status (B) = Matching (No match yet)
- If there are 2 same entries priority is given to those who create a new match first, which is determined by the recordID

If there is no match we are going to look for triplet

### Triplet match
A -> B & B -> C & C -> A  
If pair is not found, the program will search for triplet.  

When a pair or a triplet is found, their status will be updated
![image](https://github.com/Yuylam/dream-hostel/assets/147635499/9ae8f19a-02c1-485a-88b0-ab8b23438f20)

![image](https://github.com/Yuylam/dream-hostel/assets/147635499/b83d34b0-0097-42a5-89e5-cf00507a3fb3)

## Confirmation mechanism
### Accept mechanism
When a student accepts the match, their acceptance and status will be updated accordingly.
![image](https://github.com/Yuylam/dream-hostel/assets/147635499/d745cd41-ea9c-4632-acde-c48714273412)

When all students of the same match accept the match, their status will be updated to successful.
![image](https://github.com/Yuylam/dream-hostel/assets/147635499/226b6dd1-ed47-4c76-a27a-6071b5638ba6)

### Decline mechanism
When a student declines a match, the exchange is discarded, and pairing will be made.  
Example: A, B, C.   
Let's say A declined the match; the program is going to find another pair for B and C if there is one and update the changes. However, for A, we are going to delete its entry and append its entry to the table again to change the recordID so that he or she needs to queue again to get an exchange.  

## Future Improvements
Currently, we are only considering the current college and their desired college without considering the room type. In further improvements, we may include room types in consideration by considering their desired room type.
