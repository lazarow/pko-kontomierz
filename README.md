# Kontomierz dla PKO BP

Prosty skrypt do podsumowania wydatków na podstawie wyciągu z PKO w formacie CSV.

Skrypt zczytuje dane z pliku i przyporządkowuje wszystkie obciążenia konta do jednej z kategorii:
- food,
- drugs and cosmetics,
- clothes,
- charges,
- other,
- cash_machine.

W pliku rules.php znajdują się reguły dopasowujące wydatki do kategorii (na zasadzie dopasowanie tekstowego).

Aby odpalić aplikację wystarczy użyć (będąc w katalogu z projektem):
```
php -S localhost:8001
```
