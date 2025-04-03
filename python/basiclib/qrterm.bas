REM Datei zur Nutzung mit GWBASIC for MSDOS; Ausgabe im Konsolenformat!!!

DIM QRCODE(21 * 21 - 1)

REM Feld einlesen
FOR I = 0 TO (21 * 21 - 1) STEP 1
READ QRCODE(I)
NEXT I

REM Bildschirminititalisierung
SCREEN 0
COLOR 0, 7
LOCATE , , 0
CLS

REM Ausgabe Code mittels Blockzeichen 219 (ggf. anpassen!)
LINESTR$ = ""
LINENUM% = 2
LOCATE LINENUM%, 19
FOR I = 0 TO (21 * 21 - 1) STEP 1
IF (QRCODE(I) = 1) THEN LINESTR$ = LINESTR$ + CHR$(219) + CHR$(219) ELSE LINESTR$ = LINESTR$ + "  "
IF (LEN(LINESTR$) >= 42) THEN PRINT LINESTR$: LINENUM% = LINENUM% + 1: LINESTR$ = "": LOCATE LINENUM%, 19
NEXT I

REM Programm endlos laufen lassen
WHILE 1
        IF (INPUT$(4) = "quit") THEN
                STOP
        END IF
WEND
