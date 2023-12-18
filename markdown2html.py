#!/usr/bin/python3
"""
Converts Markdown to HTML
"""

import sys
import os
import markdown
import re
import hashlib

def convert_markdown_to_html(markdown_file, html_file):
    """
    Converts Markdown to HTML
    """
    try:
        with open(markdown_file, 'r') as md_file:
            md_content = md_file.read()
            html_content = parse_markdown(md_content)
            with open(html_file, 'w') as html_out:
                html_out.write(html_content)
    except FileNotFoundError:
        print(f"Missing {markdown_file}", file=sys.stderr)
        sys.exit(1)

def parse_markdown(markdown_content):
    """
    Parses Markdown content to HTML
    """
    # Parse Headings
    headings_pattern = re.compile(r'^(\#{1,6})\s(.+)$', re.MULTILINE)
    markdown_content = headings_pattern.sub(r'<\1>\2</\1>', markdown_content)

    # Parse Unordered Listing
    unordered_list_pattern = re.compile(r'^\s*\-\s(.+)$', re.MULTILINE)
    markdown_content = unordered_list_pattern.sub(r'<ul>\n<li>\1</li>\n</ul>', markdown_content)

    # Parse Ordered Listing
    ordered_list_pattern = re.compile(r'^\s*\*\s(.+)$', re.MULTILINE)
    markdown_content = ordered_list_pattern.sub(r'<ol>\n<li>\1</li>\n</ol>', markdown_content)

    # Parse Simple Text
    text_pattern = re.compile(r'^\s*([^#-\*\[\(]+)\s*$', re.MULTILINE)
    markdown_content = text_pattern.sub(r'<p>\1</p>', markdown_content)
    markdown_content = re.sub(r'\n\n', '<br/>\n', markdown_content)

    # Parse Bold and Emphasis Text
    bold_pattern = re.compile(r'\*\*(.+?)\*\*', re.MULTILINE)
    markdown_content = bold_pattern.sub(r'<b>\1</b>', markdown_content)
    emphasis_pattern = re.compile(r'__(.+?)__', re.MULTILINE)
    markdown_content = emphasis_pattern.sub(r'<em>\1</em>', markdown_content)

    # Parse Special Syntax
    special_syntax_pattern = re.compile(r'\[\[(.+?)\]\]', re.MULTILINE)
    markdown_content = special_syntax_pattern.sub(lambda match: hashlib.md5(match.group(1).encode()).hexdigest(), markdown_content)
    special_syntax_pattern = re.compile(r'\(\((.+?)\)\)', re.MULTILINE)
    markdown_content = special_syntax_pattern.sub(lambda match: match.group(1).replace('c', ''), markdown_content)

    return markdown_content

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Usage: ./markdown2html.py <MarkdownFile> <OutputFile>", file=sys.stderr)
        sys.exit(1)

    markdown_file = sys.argv[1]
    html_file = sys.argv[2]

    if not os.path.exists(markdown_file):
        print(f"Missing {markdown_file}", file=sys.stderr)
        sys.exit(1)

    convert_markdown_to_html(markdown_file, html_file)
    sys.exit(0)
