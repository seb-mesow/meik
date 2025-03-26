import os
import tempfile
import sys
import qrcode

temp_dir = os.path.join(tempfile.gettempdir(), 'meik', 'qr_code', 'basic_script')
os.makedirs(temp_dir, exist_ok=True)
temp_file = tempfile.NamedTemporaryFile(mode='w', encoding="cp1252", dir=temp_dir, delete=False)

row_number = 1
all_lines = []

data = sys.argv[0]
img = qrcode.make(data, version=1, error_correction=qrcode.ERROR_CORRECT_Q, box_size=1, border=0)

all_lines.append(f"{row_number} REM Dies ist GW-BASIC.")
row_number += 1

for i in range(21):
	row = []
	for j in range(21):
		if img.getpixel((i,j)) == 0:
			row.append(1)
		else:
			row.append(0)
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

temp_file.write("\n".join(all_lines))
temp_file.close()
print(temp_file.name)
