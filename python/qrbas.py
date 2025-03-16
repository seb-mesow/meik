import qrcode

img = qrcode.make("0123456789", version=1, error_correction=qrcode.ERROR_CORRECT_Q, box_size=1, border=0)
basfile = open("qr.bas", 'w', encoding="cp1252")
row_number = 1
all_lines = []

for i in range(21):
    row = []
    for j in range(21):
        if img.getpixel((i,j)) == 0: row.append(1)
        else: row.append(0)
    all_lines.append(f"{row_number} DATA {str(row)[1:-1].replace(' ', '')}")
    row_number += 1

program_lines = [
    'DIM QRCODE(21*21-1)',
    'FOR I = 0 TO (21*21-1) STEP 1',
    'READ QRCODE(I)',
    'NEXT I',
    'LOCATE ,,0',
    'CLS',
    'LINESTR$=""',
    'LINENUM%=2',
    'LOCATE LINENUM%,19',
    'FOR I = 0 TO (21*21-1) STEP 1',
    'IF (QRCODE(I)=1) THEN LINESTR$ = LINESTR$ + CHR$(219) + CHR$(219) ELSE LINESTR$ = LINESTR$ + "  "',
    'IF (LEN(LINESTR$)>=42) THEN PRINT LINESTR$: LINENUM% = LINENUM% + 1: LINESTR$ = "": LOCATE LINENUM%,19',
    'NEXT I',
    'WHILE 1',
    'WEND'
]

for line in program_lines:
    all_lines.append(f"{row_number} {line}")
    row_number += 1

basfile.writelines("\n".join(all_lines))