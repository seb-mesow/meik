REM Datei zur Nutzung mit QBASIC for MSDOS!!!

DIM QRCODE(21 * 21 - 1)

REM Feld einlesen
FOR I = 0 TO (21 * 21 - 1) STEP 1
        READ QRCODE(I)
NEXT I

REM Konfigurationsdaten
BOX.SIZE% = 10
BOX.PAD% = BOX.SIZE% * 4

REM Bildschirminititalisierung, gegebenenfalls Bildschirmmodus anpassen!
SCREEN 12

BOX.FULLWIDTH% = (BOX.PAD% * 2) + (21 * BOX.SIZE%)
BOX.FULLHEIGHT% = BOX.FULLWIDTH%

REM Startposition berechnen
BOX.POS.X% = (640 - BOX.FULLWIDTH%) / 2
BOX.POS.Y% = (480 - BOX.FULLHEIGHT%) / 2

REM Ausgabe Code mittels VGA-Grafik
CLS
LINE (BOX.POS.X%, BOX.POS.Y%)-(BOX.POS.X% + BOX.FULLWIDTH%, BOX.POS.Y% + BOX.FULLHEIGHT%), 15, BF

BOX.Y% = BOX.POS.Y% + BOX.PAD% - BOX.SIZE%
FOR I% = 0 TO (21 * 21 - 1) STEP 1
        IF (I% MOD 21 = 0) THEN BOX.Y% = BOX.Y% + BOX.SIZE%

        IF (QRCODE(I%) = 1) THEN
                BOX.X% = ((I% MOD 21) * BOX.SIZE%) + BOX.POS.X% + BOX.PAD%
                BOX.X2% = BOX.X% + BOX.SIZE%
                BOX.Y2% = BOX.Y% + BOX.SIZE%

                LINE (BOX.X%, BOX.Y%)-(BOX.X2%, BOX.Y2%), 0, BF
        END IF
NEXT I%

WHILE 1
        IF (INPUT$(4) = "quit") THEN
                STOP
        END IF
WEND
