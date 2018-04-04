# LaravelAPI  
A Linear Node Problem Solution:  
  
It takes two inputs - Boss(x) And Employee(y).  
  
**x can be boss of y if and only if:**  
_1. x isn't boss of any one else (As it is linear chain)._\n  
_2. y isn't employee of someone else other than x._  
  
-->So, a json object is returned either for session('success') that _relation has been created._  
-->Otherwise, if that relation directly or indirectly is already present in database, it returns that _realtion is already present._  
-->Otherwise an error is returned that _relation isn't possible._  
