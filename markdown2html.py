import sys


def ol_parse(index, lines_read_list):
    list_ol = ['<ol>\n']
    while (index < len(lines_read_list)):
        if lines_read_list[index][0] != '*':
            break

        data = lines_read_list[index].strip()
        string_to_parsing = data.lstrip("*").strip()
        list_ol.append(f'  <li>{string_to_parsing}</li>\n')
        index += 1
    list_ol.append('</ol>\n')
    return (index, list_ol)


def ul_parse(index, lines_read_list):
    list_ul = ['<ul>\n']
    while (index < len(lines_read_list)):
        if lines_read_list[index][0] != '-':
            break

        data = lines_read_list[index].strip()
        string_to_parsing = data.lstrip("-").strip()
        list_ul.append(f'  <li>{string_to_parsing}</li>\n')
        index += 1
    list_ul.append('</ul>\n')
    return (index, list_ul)


def heading_parse(index, lines_read_list):
    """heading tags"""
    count_heading = 0
    list_heading = []
    min_level = 1
    max_level = 6

    while (index < len(lines_read_list)):
        if lines_read_list[index][0] != '#':
            break
        
        data = lines_read_list[index].strip()
        heading_level = len(data) - len(data.lstrip('#'))

        if min_level <= heading_level <= max_level:
            string_to_parsing = data.lstrip("#").strip()
            list_heading.append(f'<h{heading_level}>{string_to_parsing}</h{heading_level}>\n')

        index += 1

    return (index, list_heading)


funtion_parsing = {
    '#': heading_parse,
    '-': ul_parse,
    '*': ol_parse,
}

if __name__ == '__main__':
    if len(sys.argv) < 3:
        sys.stderr.write('Usage: ./markdown2html.py README.md README.html\n')
        sys.exit(1)
    try:
        htmlTagList = []
        with open(sys.argv[1], 'r') as markdownFile:
            lines_read_list = markdownFile.readlines()
            index = 0
            while (index < len(lines_read_list)):
                line = lines_read_list[index].strip()
                first_char = line[0]
                if first_char in funtion_parsing.keys():
                    (index, htmlTag) = funtion_parsing[first_char](index, lines_read_list)
                else:
                    htmlTag = 'parrafo\n'
                    index += 1
                htmlTagList.append(htmlTag)

            print(htmlTagList)

        with open(sys.argv[2], 'w', encoding="utf-8") as html:
            for htmlLines in htmlTagList:
                for html_tag in htmlLines:
                    if html_tag:
                        html.write(html_tag)
        sys.exit(0)
    except FileNotFoundError:
        sys.stderr.write('Missing {}\n'.format(sys.argv[1]))
        sys.exit(1)
