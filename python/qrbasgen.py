import tempfile
import pathlib
import zipfile
import qrcode
import shutil
import sys

basic_line_current = 10

def generate_data_script(data: str) -> list[str]:
    global basic_line_current
    qrcode_img = qrcode.make(data, version=1, error_correction=qrcode.ERROR_CORRECT_Q, box_size=1, border=0)
    qrcode_resolution = qrcode_img.width

    basic_data_lines = []
    for i in range(qrcode_resolution):
        basic_data_row = []
        for j in range(qrcode_resolution):
            if qrcode_img.getpixel((i, j)) == 0: basic_data_row.append("1")
            else: basic_data_row.append("0")
        basic_data_lines.append(f"{basic_line_current} DATA {",".join(basic_data_row)}\r\n")
        basic_line_current += 10

    return basic_data_lines

def generate_terminal_script(data_script: list[str]) -> list[str]:
    global basic_line_current
    full_script = []
    for line in data_script: full_script.append(line)
    with open(pathlib.Path("basiclib").joinpath("qrterm.bas"), "r") as term_lib_handle:
        lines = term_lib_handle.readlines()
    for line in lines:
        full_script.append(f"{basic_line_current} {line}")
        basic_line_current += 10
    return full_script

def generate_vga_script(data_script: list[str]) -> list[str]:
    global basic_line_current
    full_script = []
    for line in data_script: full_script.append(line)
    with open(pathlib.Path("basiclib").joinpath("qrvga.bas"), "r") as vga_lib_handle:
        lines = vga_lib_handle.readlines()
    for line in lines:
        full_script.append(f"{basic_line_current} {line.replace("\n", "\r\n")}")
        basic_line_current += 10
    return full_script

def generate_copy_archive() -> None:
    #generate temp directory for creation of archive
    _TEMPDIR = tempfile.mkdtemp(prefix="qrbasgen")

    #generate archive file from runtime dir
    archive_path = pathlib.Path(_TEMPDIR).joinpath("qrgen.zip")
    with zipfile.ZipFile(archive_path, "x") as archive_handle:
        for path, dirs, files in pathlib.Path("runtimefiles").walk():
            #add dirs to archive
            for dir in dirs:
                path_dir = pathlib.Path(path).joinpath(dir)
                path_arcdir = pathlib.Path(*path_dir.parts[1:])
                print("Adding dir to archive:", path_arcdir)
                archive_handle.mkdir(dir)

            #add files to archive
            for file in files:
                path_file = pathlib.Path(path).joinpath(file)
                path_arcfile = pathlib.Path(*path_file.parts[1:])
                print("Adding file to archive:", path_arcfile)
                archive_handle.write(path_file, path_arcfile)

    #copy archive and remove temp dir
    shutil.copyfile(archive_path, "qrgen.zip")
    shutil.rmtree(_TEMPDIR)

def main(args: list[str]) -> int:
    if len(args) != 3:
        print(f"Usage: python3 {__file__} [mode] [data]")
        print("mode:\tQ (QBASIC), G (GWBASIC)")
        print("data:\tData value to encode; must be integer in range 0-9999999999")
        return 1

    try:
        generator_mode = str(args[1])
        generator_data = int(args[2])
    except:
        raise ValueError("Invalid arguments used!")
    
    if not generator_data in range(0, 10000000000):
        raise ValueError("Invalid data value! Must be in range 0-9999999999")

    #choose generator method: tty or (s)vga
    data_script_lines = generate_data_script(str(generator_data))
    if generator_mode == "G":
        script_lines = generate_terminal_script(data_script_lines)
    elif generator_mode == "Q":
        script_lines = generate_vga_script(data_script_lines)
    else: raise ValueError("Unexpected mode!")

    #write basic file
    with open(pathlib.Path(f"OUT_{generator_mode}.BAS"), "w", encoding="latin-1") as output_file_handle:
        output_file_handle.writelines(script_lines)

    #regular exit
    return 0

#run main module with cmdline args
if __name__ == "__main__":
    exit_code = main(sys.argv)
    sys.exit(exit_code)
