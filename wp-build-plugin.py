import os, shutil

CWD = os.getcwd()


class Build:

    dirname = ""
    parent = ""
    ignored = []
    files = []

    def __init__(self, dirname: str):
        self.parent = os.path.abspath(os.path.join(dirname, os.pardir))
        self.dirname = dirname
        self.ignored = self.get_ignored()
        self.run()

    def is_file(self, path):
        file = os.path.join(self.dirname, path)
        return file if os.path.isfile(file) else False

    def is_dir(self, path):
        dir = os.path.join(self.dirname, path)
        return dir if os.path.isdir(dir) else False

    def run_gulp(self):
        if self.is_file("gulpfile.js"):
            print(os.popen("gulp").read())

    def get_ignored(self):
        ignored = []
        gitignore = self.is_file(".gitignore")
        if gitignore:
            contents = open(gitignore, "r")
            ignored = [
                os.path.join(self.dirname, fname)
                for fname in contents.read().split("\n")
            ]
            contents.close()
        return ignored

    def dist_files(self):
        dir = self.dirname
        dist = os.path.join(dir, "dist")
        files = [f.path for f in os.scandir(dir) if not f.path in self.ignored]
        if not os.path.isdir(dist):
            os.mkdir(dist)
        for file in files:
            basename = os.path.basename(file)
            newfile = os.path.join(dist, basename)
            if self.is_file(basename):
                shutil.copy(file, newfile)
            elif self.is_dir(basename):
                shutil.copytree(file, newfile, dirs_exist_ok=True)
        return dist

    def build(self):
        basename = os.path.basename(self.dirname)
        dist = self.dist_files()
        archive = shutil.make_archive(basename, "zip", self.parent, dist)
        return archive

    def run(self):
        os.chdir(CWD)
        self.run_gulp()
        build = self.build()
        print(f'Created plugin archive at "{build}"')


build = Build(CWD)
